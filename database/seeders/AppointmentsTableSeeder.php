<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppointmentsTableSeeder extends Seeder
{
    public function run()
    {
        $appointments = [];
        $patients = DB::table('patients')->pluck('id');
        $doctors = DB::table('doctors')->pluck('id');
        $rooms = DB::table('consultation_rooms')->pluck('id');
        $statuses = ['programada', 'confirmada', 'en_curso', 'completada', 'cancelada'];
        
        for ($i = 0; $i < 100; $i++) {
            $scheduledAt = Carbon::now()
                ->addDays(rand(-30, 30))
                ->setTime(rand(8, 18), rand(0, 3) * 15, 0);
                
            $appointments[] = [
                'id' => Str::uuid(),
                'patient_id' => $patients[rand(0, count($patients) - 1)],
                'doctor_id' => $doctors[rand(0, count($doctors) - 1)],
                'consultation_room_id' => $rooms[rand(0, count($rooms) - 1)],
                'scheduled_at' => $scheduledAt,
                'status' => $statuses[rand(0, 4)],
                'notes' => rand(0, 1) ? 'Paciente con ' . ['fiebre', 'dolor', 'malestar general'][rand(0, 2)] : null,
                'duration_minutes' => [15, 30, 45, 60][rand(0, 3)],
                'created_at' => $scheduledAt->copy()->subDays(rand(1, 10)),
                'updated_at' => $scheduledAt->copy()->subDays(rand(0, 5)),
            ];
        }

        DB::table('appointments')->insert($appointments);
    }
}