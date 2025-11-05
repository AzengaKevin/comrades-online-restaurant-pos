<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(array $data)
    {
        $attributes = [
            'name' => data_get($data, 'name'),
            'email' => data_get($data, 'email'),
            'phone' => data_get($data, 'phone'),
            'dob' => data_get($data, 'dob'),
            'address' => data_get($data, 'address'),
            'pin' => data_get($data, 'pin'),
            'password' => Hash::make(data_get($data, 'password', 'password')),
        ];

        return User::query()->create($attributes);
    }

    public function update(User $user, array $data)
    {
        $attributes = [
            'name' => data_get($data, 'name', $user->name),
            'email' => data_get($data, 'email', $user->email),
            'phone' => data_get($data, 'phone', $user->phone),
            'dob' => data_get($data, 'dob', $user->dob),
            'address' => data_get($data, 'address', $user->address),
            'pin' => data_get($data, 'pin', $user->pin),
        ];

        return $user->update($attributes);
    }

    public function delete(User $user, bool $force = false)
    {
        return $force ? $user->forceDelete() : $user->delete();
    }
}
