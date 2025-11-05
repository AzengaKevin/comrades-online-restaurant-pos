<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\SyncRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function index(Request $request)
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters(['name', 'email', AllowedFilter::exact('id')])
            ->allowedSorts(['name', 'email', 'created_at'])
            ->paginate($request->input('per_page', 24))
            ->appends($request->query());

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $storeUserRequest)
    {

        $data = $storeUserRequest->validated();

        $user = DB::transaction(function () use ($data) {
            return $this->userService->create($data);
        });

        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $updateUserRequest, User $user)
    {

        $data = $updateUserRequest->validated();

        DB::transaction(function () use ($user, $data) {
            $this->userService->update($user, $data);
        });

        return new UserResource($user->refresh());
    }

    public function destroy(Request $request, User $user)
    {

        $force = $request->boolean('force', false);

        DB::transaction(function () use ($user, $force) {
            $this->userService->delete($user, $force);
        });

        return response()->noContent();
    }

    public function me(Request $request)
    {

        $user = $request->user();

        return new UserResource($user);
    }

    public function sync(SyncRequest $syncRequest)
    {
        $data = $syncRequest->validated();

        $changes = DB::transaction(function () use ($data) {

            return $this->userService->sync($data);
        });

        return response()->json([
            'data' => $changes,
        ]);
    }
}
