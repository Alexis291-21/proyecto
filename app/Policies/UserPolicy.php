<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        // Solo admins ven la lista
        return $user->esAdmin();
    }

    public function create(User $user): bool
    {
        // Solo admins pueden crear
        return $user->esAdmin();
    }

    public function update(User $user, User $model): bool
    {
        // Solo admins o el propio usuario *o* recepcionista, según tu regla
        return $user->esAdmin()
            || ($user->esRecepcionista() && !$model->esAdmin())
            || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        // Impide borrarte a ti mismo y solo admins
        return $user->esAdmin() && $user->id !== $model->id;
    }
}
