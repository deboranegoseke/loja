<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_CLIENTE = 'cliente';
    public const ROLE_ADM     = 'admin';
    public const ROLE_GERENTE = 'gerente';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = ['password', 'remember_token'];
}
