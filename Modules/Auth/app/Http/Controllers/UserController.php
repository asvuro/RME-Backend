<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Http\Requests\StoreUserRequest;
use Modules\Auth\Http\Requests\UpdateUserRequest;
use Modules\Auth\Http\Resources\UserResource;
use Modules\Auth\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->string('name').'%');
        }

        return UserResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            ...$request->validated(),
            'password' => Hash::make($request->string('password')),
            'password_changed_at' => now(),
        ]);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();

        // Pertahanan lapis kedua (defense in depth): FormRequest sudah
        // exclude is_locked/is_active untuk akun sendiri, tapi jangan
        // bergantung hanya pada validasi kalau ada refactor di masa depan.
        if ($user->id === $request->user()?->id) {
            unset($data['is_locked'], $data['is_active']);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $data['password_changed_at'] = now();
        }

        $user->update($data);

        if (array_intersect_key($data, array_flip(['is_locked', 'is_active', 'password'])) !== []) {
            $user->tokens()->delete();
        }

        return new UserResource($user);
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()?->id, 422, 'Anda tidak dapat menghapus akun Anda sendiri.');

        $user->delete();

        return response()->json(null, 204);
    }
}
