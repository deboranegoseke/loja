<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    // ver listagem própria
    public function viewAny(User $user): bool
    {
        return (bool) $user;
    }

    // ver apenas o próprio
    public function view(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }

    // criar: qualquer usuário autenticado pode criar (se não tiver um)
    public function create(User $user): bool
    {
        return (bool) $user;
    }

    // atualizar: apenas o dono
    public function update(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }

    // deletar: apenas o dono (se você quiser permitir)
    public function delete(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }
}
