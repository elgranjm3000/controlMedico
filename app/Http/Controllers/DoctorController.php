<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors.
     */
    public function index(Request $request)
    {
        $query = Doctor::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('specialty', 'LIKE', "%{$search}%")
                  ->orWhere('license_number', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Filter by specialty
        if ($request->has('specialty') && !empty($request->specialty)) {
            $query->where('specialty', 'LIKE', "%{$request->specialty}%");
        }

        // Order by creation date
        $query->orderBy('created_at', 'desc');

        $doctors = $query->paginate(15)->appends($request->all());
        
        // Get unique specialties for filter
        $specialties = Doctor::select('specialty')
            ->distinct()
            ->whereNotNull('specialty')
            ->where('specialty', '!=', '')
            ->orderBy('specialty')
            ->pluck('specialty');
        
        return view('doctors.index', compact('doctors', 'specialties'));
    }

    /**
     * Show the form for creating a new doctor.
     */
    public function create()
    {
        return view('doctors.create');
    }

    /**
     * Store a newly created doctor in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'license_number' => 'required|string|max:50|unique:doctors,license_number',
            'email' => 'nullable|email|unique:doctors,email',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $doctor = Doctor::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'last_name' => $request->last_name,
            'specialty' => $request->specialty,
            'license_number' => $request->license_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor creado exitosamente.');
    }

    /**
     * Display the specified doctor.
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['appointments.patient', 'appointments.consultationRoom']);
        
        // Get appointment statistics
        $totalAppointments = $doctor->appointments()->count();
        $completedAppointments = $doctor->appointments()->where('status', 'completada')->count();
        $todayAppointments = $doctor->appointments()
            ->whereDate('scheduled_at', today())
            ->with(['patient', 'consultationRoom'])
            ->orderBy('scheduled_at')
            ->get();
        
        return view('doctors.show', compact('doctor', 'totalAppointments', 'completedAppointments', 'todayAppointments'));
    }

    /**
     * Show the form for editing the specified doctor.
     */
    public function edit(Doctor $doctor)
    {
        return view('doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified doctor in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'license_number' => 'required|string|max:50|unique:doctors,license_number,' . $doctor->id,
            'email' => 'nullable|email|unique:doctors,email,' . $doctor->id,
            'phone' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $doctor->update($request->all());

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor actualizado exitosamente.');
    }

    /**
     * Remove the specified doctor from storage.
     */
    public function destroy(Doctor $doctor)
    {
        try {
            // Check if doctor has active appointments
            $activeAppointments = $doctor->appointments()
                ->whereIn('status', ['programada', 'confirmada', 'en_curso'])
                ->count();
            
            if ($activeAppointments > 0) {
                return redirect()->route('doctors.index')
                    ->with('error', 'No se puede desactivar el doctor. Tiene citas activas programadas.');
            }
            
            // Soft delete - deactivate instead of permanent deletion
            $doctor->update(['is_active' => false]);
            
            return redirect()->route('doctors.index')
                ->with('success', 'Doctor desactivado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('doctors.index')
                ->with('error', 'Error al desactivar el doctor: ' . $e->getMessage());
        }
    }

    /**
     * Activate a deactivated doctor.
     */
    public function activate(Doctor $doctor)
    {
        $doctor->update(['is_active' => true]);
        
        return redirect()->route('doctors.index')
            ->with('success', 'Doctor activado exitosamente.');
    }

    /**
     * Get doctor data for AJAX requests.
     */
    public function getDoctorData(Doctor $doctor)
    {
        return response()->json([
            'success' => true,
            'doctor' => [
                'id' => $doctor->id,
                'full_name' => $doctor->getFullNameAttribute(),
                'name' => $doctor->name,
                'last_name' => $doctor->last_name,
                'specialty' => $doctor->specialty,
                'license_number' => $doctor->license_number,
                'email' => $doctor->email,
                'phone' => $doctor->phone,
                'is_active' => $doctor->is_active,
                'appointments_count' => $doctor->appointments()->count(),
                'created_at' => $doctor->created_at->format('d/m/Y H:i'),
                'updated_at' => $doctor->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Search doctors for autocomplete.
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        
        $doctors = Doctor::where('is_active', true)
            ->where(function($query) use ($term) {
                $query->where('name', 'LIKE', "%{$term}%")
                      ->orWhere('last_name', 'LIKE', "%{$term}%")
                      ->orWhere('specialty', 'LIKE', "%{$term}%")
                      ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$term}%"]);
            })
            ->limit(10)
            ->get()
            ->map(function($doctor) {
                return [
                    'id' => $doctor->id,
                    'text' => $doctor->getFullNameAttribute() . ' - ' . $doctor->specialty,
                    'specialty' => $doctor->specialty,
                    'license_number' => $doctor->license_number,
                ];
            });

        return response()->json($doctors);
    }

    /**
     * Get doctor's schedule for calendar.
     */
    public function getSchedule(Doctor $doctor, Request $request)
    {
        $date = $request->get('date', today());
        
        $appointments = $doctor->appointments()
            ->whereDate('scheduled_at', $date)
            ->with(['patient', 'consultationRoom'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(function($appointment) {
                return [
                    'id' => $appointment->id,
                    'time' => $appointment->scheduled_at->format('H:i'),
                    'duration' => $appointment->duration_minutes,
                    'patient' => $appointment->patient->getFullNameAttribute(),
                    'room' => $appointment->consultationRoom->name,
                    'status' => $appointment->status,
                    'notes' => $appointment->notes,
                ];
            });
        
        return response()->json([
            'success' => true,
            'appointments' => $appointments
        ]);
    }
}