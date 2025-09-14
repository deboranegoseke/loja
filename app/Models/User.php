<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    public const ROLE_CLIENTE = 'cliente';
    public const ROLE_ADM     = 'adm';
    public const ROLE_GERENTE = 'gerente';

    protected $fillable = [
        'name','email','password','role',
    ];

    protected $hidden = ['password','remember_token'];

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array($this->role, $roles, true);
    }

    // relações úteis
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function tickets(): HasMany { return $this->hasMany(SupportTicket::class); }

    public static function createUser($request)
    {
        $user = self::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => self::ROLE_CLIENTE,
        ]);

        return $user;
    }
}
