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

    private function fetchServerChanges($lastSyncedAt)
    {
        $changes = [];

        $changes['created'] = User::query()
            ->where('created_at', '>', $lastSyncedAt)
            ->get()
            ->all();

        $changes['updated'] = User::query()
            ->where('updated_at', '>', $lastSyncedAt)
            ->where('created_at', '<=', $lastSyncedAt)
            ->get()
            ->all();

        $changes['deleted'] = User::onlyTrashed()
            ->where('deleted_at', '>', $lastSyncedAt)
            ->pluck('id')
            ->all();

        return $changes;
    }

    private function updateLocalRecords($data)
    {
        User::query()->insert(collect(data_get($data, 'created', []))->map(fn ($item) => [
            'id' => data_get($item, 'id'),
            'name' => data_get($item, 'name'),
            'email' => data_get($item, 'email'),
            'phone' => data_get($item, 'phone'),
            'dob' => data_get($item, 'dob'),
            'address' => data_get($item, 'address'),
            'email_verified_at' => data_get($item, 'email_verified_at'),
            'phone_verified_at' => data_get($item, 'phone_verified_at'),
            'pin' => data_get($item, 'pin'),
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ])->all());

        User::query()->upsert(collect(data_get($data, 'updated', []))->map(fn ($item) => [
            'id' => data_get($item, 'id'),
            'name' => data_get($item, 'name'),
            'email' => data_get($item, 'email'),
            'phone' => data_get($item, 'phone'),
            'dob' => data_get($item, 'dob'),
            'address' => data_get($item, 'address'),
            'email_verified_at' => data_get($item, 'email_verified_at'),
            'phone_verified_at' => data_get($item, 'phone_verified_at'),
            'pin' => data_get($item, 'pin'),
            'updated_at' => now(),
        ])->all(), ['id'], [
            'name',
            'email',
            'phone',
            'dob',
            'address',
            'pin',
            'email_verified_at',
            'phone_verified_at',
            'pin',
            'updated_at',
        ]);

        User::query()->whereIn('id', data_get($data, 'deleted', []))->delete();
    }

    public function sync(array $data = [])
    {
        $changes = $this->fetchServerChanges(data_get($data, 'last_synced_at'));

        $this->updateLocalRecords($data);

        return $changes;
    }
}
