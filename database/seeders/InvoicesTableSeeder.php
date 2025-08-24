<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvoicesTableSeeder extends Seeder
{
    public function run()
    {
        $invoices = [];
        $patients = DB::table('patients')->pluck('id');
        $appointments = DB::table('appointments')->where('status', 'completada')->pluck('id');
        $users = DB::table('users')->pluck('id');
        $paymentMethods = ['efectivo', 'transferencia', 'credito'];
        $statuses = ['pendiente', 'pagada', 'cancelada'];
        
        $invoiceNumber = 1000;
        
        for ($i = 0; $i < 50; $i++) {
            $subtotal = rand(500, 5000);
            $tax = $subtotal * 0.16;
            $total = $subtotal + $tax;
            
            $invoices[] = [
                'id' => Str::uuid(),
                'invoice_number' => 'FACT-' . $invoiceNumber++,
                'patient_id' => $patients[rand(0, count($patients) - 1)],
                'appointment_id' => rand(0, 1) ? $appointments[rand(0, count($appointments) - 1)] : null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $paymentMethods[rand(0, 2)],
                'status' => $statuses[rand(0, 2)],
                'due_date' => Carbon::now()->addDays(rand(1, 30)),
                'notes' => rand(0, 1) ? 'Factura por servicios médicos' : null,
                'created_by' => $users[rand(0, count($users) - 1)],
                'created_at' => Carbon::now()->subDays(rand(0, 60)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30)),
            ];
        }

        DB::table('invoices')->insert($invoices);
    }
}