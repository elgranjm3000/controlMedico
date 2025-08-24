<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'description', 'code', 'category', 'unit_price',
        'current_stock', 'minimum_stock', 'unit_measure', 'is_active'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isLowStock()
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}