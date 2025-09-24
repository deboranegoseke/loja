<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_CLIENTE = 'cliente';
    public const ROLE_ADM     = 'adm';
    public const ROLE_GERENTE = 'gerente';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Verifica se o usuário tem um dos papéis informados.
     *
     * @param  string|array  $roles
     * @return bool
     */
    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array($this->role, $roles, true);
    }
}
