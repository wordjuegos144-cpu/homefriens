<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owner extends Authenticatable
{
    use HasFactory;

    protected $table = 'owners';

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];
}
