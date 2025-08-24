<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\ConsultationRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'consultationRoom']);
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            })->orWhereHas('doctor', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by doctor
        if ($request->has('doctor') && !empty($request->doctor)) {
            $query->where('doctor_id', $request->doctor);
        }

        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        // Default to today if no filters
        if (!$request->hasAny(['search', 'status', 'doctor', 'date_from', 'date_to'])) {
            $query->whereDate('scheduled_at', '>=', today());
        }

        // Order by scheduled date
        $query->orderBy('scheduled_at', 'asc');

        $appointments = $query->paginate(15)->appends($request->all());
        
        // Get data for filters
        $doctors = Doctor::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('appointments.index', compact('appointments', 'doctors'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(Request $request)
    {
        $patients = Patient::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $doctors = Doctor::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $consultationRooms = ConsultationRoom::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Pre-select patient if provided
        $selectedPatient = null;
        if ($request->has('patient_id')) {
            $selectedPatient = Patient::find($request->patient_id);
        }

        return view('appointments.create', compact('patients', 'doctors', 'consultationRooms', 'selectedPatient'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:patients,id',
            'doctor_id' => 'required|uuid|exists:doctors,id',
            'consultation_room_id' => 'required|uuid|exists:consultation_rooms,id',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|numeric|min:15|max:480',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for conflicts
        $scheduledAt = Carbon::parse($request->scheduled_at);
        $endTime = $scheduledAt->copy()->addMinutes($request->duration_minutes);
        
        $conflicts = $this->checkForConflicts(
            $request->doctor_id,
            $request->consultation_room_id,
            $scheduledAt,
            $endTime
        );

        if ($conflicts['has_conflicts']) {
            return redirect()->back()
                ->withErrors(['scheduled_at' => $conflicts['message']])
                ->withInput();
        }

        $appointment = Appointment::create([
            'id' => Str::uuid(),
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'consultation_room_id' => $request->consultation_room_id,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $request->duration_minutes,
            'status' => 'programada',
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Cita creada exitosamente.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'consultationRoom', 'invoice']);
        
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $doctors = Doctor::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $consultationRooms = ConsultationRoom::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors', 'consultationRooms'));
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:patients,id',
            'doctor_id' => 'required|uuid|exists:doctors,id',
            'consultation_room_id' => 'required|uuid|exists:consultation_rooms,id',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|numeric|min:15|max:480',
            'status' => 'required|in:programada,confirmada,en_curso,completada,cancelada',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Only check conflicts if date/time changed and not completed/cancelled
        if (!in_array($request->status, ['completada', 'cancelada'])) {
            $scheduledAt = Carbon::parse($request->scheduled_at);
            $endTime = $scheduledAt->copy()->addMinutes($request->duration_minutes);
            
            if ($appointment->scheduled_at != $scheduledAt || 
                $appointment->doctor_id != $request->doctor_id ||
                $appointment->consultation_room_id != $request->consultation_room_id) {
                
                $conflicts = $this->checkForConflicts(
                    $request->doctor_id,
                    $request->consultation_room_id,
                    $scheduledAt,
                    $endTime,
                    $appointment->id
                );

                if ($conflicts['has_conflicts']) {
                    return redirect()->back()
                        ->withErrors(['scheduled_at' => $conflicts['message']])
                        ->withInput();
                }
            }
        }

        $appointment->update($request->all());

        return redirect()->route('appointments.index')
            ->with('success', 'Cita actualizada exitosamente.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        try {
            // Check if appointment has an invoice
            if ($appointment->invoice) {
                return redirect()->route('appointments.index')
                    ->with('error', 'No se puede eliminar la cita. Ya tiene una factura asociada.');
            }

            $appointment->delete();
            
            return redirect()->route('appointments.index')
                ->with('success', 'Cita eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('appointments.index')
                ->with('error', 'Error al eliminar la cita: ' . $e->getMessage());
        }
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:programada,confirmada,en_curso,completada,cancelada',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $appointment->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $appointment->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de la cita actualizado exitosamente.',
            'appointment' => [
                'id' => $appointment->id,
                'status' => $appointment->status,
                'notes' => $appointment->notes,
            ]
        ]);
    }

    /**
     * Get appointments for calendar view.
     */
    public function getCalendarData(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        
        $appointments = Appointment::with(['patient', 'doctor', 'consultationRoom'])
            ->whereBetween('scheduled_at', [$start, $end])
            ->get()
            ->map(function($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->patient->getFullNameAttribute() . ' - ' . $appointment->doctor->getFullNameAttribute(),
                    'start' => $appointment->scheduled_at->toISOString(),
                    'end' => $appointment->scheduled_at->copy()->addMinutes($appointment->duration_minutes)->toISOString(),
                    'backgroundColor' => $this->getStatusColor($appointment->status),
                    'borderColor' => $this->getStatusColor($appointment->status),
                    'extendedProps' => [
                        'patient' => $appointment->patient->getFullNameAttribute(),
                        'doctor' => $appointment->doctor->getFullNameAttribute(),
                        'room' => $appointment->consultationRoom->name,
                        'status' => $appointment->status,
                        'notes' => $appointment->notes,
                    ]
                ];
            });

        return response()->json($appointments);
    }

    /**
     * Check for scheduling conflicts.
     */
    private function checkForConflicts($doctorId, $roomId, $startTime, $endTime, $excludeId = null)
    {
        $query = Appointment::where(function($q) use ($doctorId, $roomId) {
            $q->where('doctor_id', $doctorId)
              ->orWhere('consultation_room_id', $roomId);
        })
        ->where('status', '!=', 'cancelada')
        ->where(function($q) use ($startTime, $endTime) {
            $q->whereBetween('scheduled_at', [$startTime, $endTime])
              ->orWhere(function($subQ) use ($startTime, $endTime) {
                  $subQ->where('scheduled_at', '<', $startTime)
                       ->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?', [$startTime]);
              });
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $conflicts = $query->with(['patient', 'doctor', 'consultationRoom'])->get();

        if ($conflicts->count() > 0) {
            $conflictMessages = [];
            
            foreach ($conflicts as $conflict) {
                if ($conflict->doctor_id == $doctorId) {
                    $conflictMessages[] = "El doctor {$conflict->doctor->getFullNameAttribute()} ya tiene una cita programada a las {$conflict->scheduled_at->format('H:i')}";
                }
                
                if ($conflict->consultation_room_id == $roomId) {
                    $conflictMessages[] = "La sala {$conflict->consultationRoom->name} ya está ocupada a las {$conflict->scheduled_at->format('H:i')}";
                }
            }

            return [
                'has_conflicts' => true,
                'message' => implode('. ', array_unique($conflictMessages)),
                'conflicts' => $conflicts
            ];
        }

        return ['has_conflicts' => false];
    }

    /**
     * Get color for appointment status.
     */
    private function getStatusColor($status)
    {
        $colors = [
            'programada' => '#fbbf24', // yellow
            'confirmada' => '#3b82f6', // blue
            'en_curso' => '#10b981', // green
            'completada' => '#059669', // dark green
            'cancelada' => '#ef4444', // red
        ];

        return $colors[$status] ?? '#6b7280'; // gray default
    }

    /**
     * Get available time slots for a doctor and room.
     */
    public function getAvailableSlots(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $roomId = $request->get('room_id');
        $date = $request->get('date', today());
        $duration = (int) $request->get('duration', 30);
        
        $startHour = 8; // 8:00 AM
        $endHour = 18; // 6:00 PM
        $slotInterval = 30; // 30 minutes
        
        $availableSlots = [];
        $occupiedSlots = [];
        
        // Get existing appointments for the day
        $appointments = Appointment::where(function($q) use ($doctorId, $roomId) {
            $q->where('doctor_id', $doctorId)
              ->orWhere('consultation_room_id', $roomId);
        })
        ->whereDate('scheduled_at', $date)
        ->where('status', '!=', 'cancelada')
        ->get();
        
        // Mark occupied time slots
        foreach ($appointments as $appointment) {
            $startTime = $appointment->scheduled_at;
            $endTime = $startTime->copy()->addMinutes($appointment->duration_minutes);
            
            $occupiedSlots[] = [
                'start' => $startTime,
                'end' => $endTime
            ];
        }
        
        // Generate available slots
        $currentTime = Carbon::parse($date)->setHour($startHour)->setMinute(0);
        $dayEnd = Carbon::parse($date)->setHour($endHour)->setMinute(0);
        
        while ($currentTime->copy()->addMinutes($duration)->lte($dayEnd)) {
            $slotEnd = $currentTime->copy()->addMinutes($duration);
            $isAvailable = true;
            
            // Check if this slot conflicts with any occupied slots
            foreach ($occupiedSlots as $occupied) {
                if ($currentTime->lt($occupied['end']) && $slotEnd->gt($occupied['start'])) {
                    $isAvailable = false;
                    break;
                }
            }
            
            // Don't allow past appointments
            if ($currentTime->isPast()) {
                $isAvailable = false;
            }
            
            if ($isAvailable) {
                $availableSlots[] = [
                    'time' => $currentTime->format('H:i'),
                    'value' => $currentTime->format('Y-m-d H:i:s'),
                    'display' => $currentTime->format('g:i A')
                ];
            }
            
            $currentTime->addMinutes($slotInterval);
        }
        
        return response()->json([
            'success' => true,
            'slots' => $availableSlots
        ]);
    }
}