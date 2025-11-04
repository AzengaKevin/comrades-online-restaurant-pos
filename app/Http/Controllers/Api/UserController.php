<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters(['name', 'email', AllowedFilter::exact('id')])
            ->allowedSorts(['name', 'email', 'created_at'])
            ->paginate($request->input('per_page', 24))
            ->appends($request->query());

        return UserResource::collection($users);
    }

    public function me(Request $request)
    {

        $user = $request->user();

        return new UserResource($user);
    }
}
