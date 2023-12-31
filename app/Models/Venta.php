<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable=[
        'producto_id',
        'cantidad',
        'total_venta'
    ];

    protected $table="ventas";

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
