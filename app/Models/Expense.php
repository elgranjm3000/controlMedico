<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Expense extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'supplier_name', 'invoice_number', 'amount', 'category',
        'description', 'expense_date', 'payment_method', 'registered_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}