<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apartment extends Model
{
	// Si tu tabla se llama 'apartamentos' reemplaza por: protected $table = 'apartamentos';
	// Por defecto usará 'apartments'
	protected $table = 'apartamentos';

	// Permitir asignación masiva; reemplaza con los campos reales de tu tabla.
	protected $fillable = ['title', 'description', 'price', 'address', 'user_id'];
}
