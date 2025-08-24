<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'patient_id', 'doctor_id', 'consultation_room_id',
        'scheduled_at', 'status', 'notes', 'duration_minutes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function consultationRoom()
    {
        return $this->belongsTo(ConsultationRoom::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}