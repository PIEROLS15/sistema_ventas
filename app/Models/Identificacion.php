<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Identificacion extends Model
{
    use HasFactory;

    protected $table = 'identificaciones';

    protected $fillable = ['tipo'];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'identificacion_id');
    }
}