<?php

namespace Cms\Http\Controllers\Api;

use Cms\Http\Controllers\Controller;
use Cms\Models\User;
use Cms\Support\CmsAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function me(): JsonResponse
    {
        $user = CmsAuth::user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'data' => $this->formatUser($user),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 422);
        }

        CmsAuth::login($user);

        return response()->json([
            'message' => 'Signed in successfully.',
            'data' => $this->formatUser($user),
        ]);
    }

    public function logout(): JsonResponse
    {
        CmsAuth::logout();

        return response()->json(['message' => 'Signed out successfully.']);
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'can_edit' => CmsAuth::canEdit(),
            'is_admin' => CmsAuth::isAdmin(),
        ];
    }
}
