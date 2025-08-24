<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    /**
     * Display a listing of patients.
     */
    public function index(Request $request)
    {
        $query = Patient::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('rfc_nit', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Filter by gender
        if ($request->has('gender') && !empty($request->gender)) {
            $query->where('gender', $request->gender);
        }

        // Order by creation date
        $query->orderBy('created_at', 'desc');

        $patients = $query->paginate(15)->appends($request->all());
        
        return view('modules.patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create()
    {
        return view('modules.patients.form');
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email',
            'phone' => 'required|string|max:20',
            'rfc_nit' => 'nullable|string|max:18|unique:patients,rfc_nit',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:masculino,femenino,otro',
            'medical_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $patient = Patient::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'rfc_nit' => $request->rfc_nit,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'medical_notes' => $request->medical_notes,
            'is_active' => true,
        ]);

        return redirect()->route('patients.index')
            ->with('success', 'Paciente creado exitosamente.');
    }

    /**
     * Display the specified patient.
     */
    public function show(Patient $patient)
    {
        $patient->load(['appointments.doctor', 'appointments.consultationRoom', 'invoices']);
        
        return view('modules.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Patient $patient)
    {
        return view('modules.patients.form', compact('patient'));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email,' . $patient->id,
            'phone' => 'required|string|max:20',
            'rfc_nit' => 'nullable|string|max:18|unique:patients,rfc_nit,' . $patient->id,
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:masculino,femenino,otro',
            'medical_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $patient->update($request->all());

        return redirect()->route('patients.index')
            ->with('success', 'Paciente actualizado exitosamente.');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(Patient $patient)
    {
        try {
            // Soft delete - deactivate instead of permanent deletion
            $patient->update(['is_active' => false]);
            
            return redirect()->route('patients.index')
                ->with('success', 'Paciente desactivado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('patients.index')
                ->with('error', 'Error al desactivar el paciente: ' . $e->getMessage());
        }
    }

    /**
     * Activate a deactivated patient.
     */
    public function activate(Patient $patient)
    {
        $patient->update(['is_active' => true]);
        
        return redirect()->route('patients.index')
            ->with('success', 'Paciente activado exitosamente.');
    }

    /**
     * Get patient data for AJAX requests.
     */
    public function getPatientData(Patient $patient)
    {
        return response()->json([
            'success' => true,
            'patient' => [
                'id' => $patient->id,
                'full_name' => $patient->getFullNameAttribute(),
                'name' => $patient->name,
                'last_name' => $patient->last_name,
                'email' => $patient->email,
                'phone' => $patient->phone,
                'rfc_nit' => $patient->rfc_nit,
                'address' => $patient->address,
                'birth_date' => $patient->birth_date?->format('Y-m-d'),
                'gender' => $patient->gender,
                'gender_label' => ucfirst($patient->gender),
                'medical_notes' => $patient->medical_notes,
                'is_active' => $patient->is_active,
                'created_at' => $patient->created_at->format('d/m/Y H:i'),
                'updated_at' => $patient->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Search patients for autocomplete.
     */
    public function search(Request $request)
    {
        $term = $request->get('term', '');
        
        $patients = Patient::where('is_active', true)
            ->where(function($query) use ($term) {
                $query->where('name', 'LIKE', "%{$term}%")
                      ->orWhere('last_name', 'LIKE', "%{$term}%")
                      ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$term}%"]);
            })
            ->limit(10)
            ->get()
            ->map(function($patient) {
                return [
                    'id' => $patient->id,
                    'text' => $patient->getFullNameAttribute(),
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                ];
            });

        return response()->json($patients);
    }
}