<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    
    protected $fillable = [
        'codigo',
        'cliente_nombre',
        'identificacion_id',
        'numero_identificacion',
        'cliente_correo',
        'vendedor_id',
        'monto_total',
        'fecha_venta',
    ];

    public function identificacion()
    {
        return $this->belongsTo(Identificacion::class);
    }

    public function detalles()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
}