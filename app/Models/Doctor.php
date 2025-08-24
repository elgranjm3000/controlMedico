<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'last_name', 'specialty', 'license_number', 
        'email', 'phone', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getFullNameAttribute()
    {
        return 'Dr. ' . $this->name . ' ' . $this->last_name;
    }
}