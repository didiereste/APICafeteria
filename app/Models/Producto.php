<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable=[
        'nombre_producto',
        'referencia',
        'precio',
        'peso',
        'categoria',
        'stock'
    ];

    protected $table="productos";

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
