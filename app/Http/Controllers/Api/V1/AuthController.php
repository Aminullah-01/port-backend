<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'data' => null,
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'data' => new UserResource($request->user()),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided current password does not match.'],
            ]);
        }

        $user->password = $request->password;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
            'data' => null,
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We could not find an account with that email address.'],
            ]);
        }

        // Generate a 6-digit numeric reset code
        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        try {
            Mail::raw(
                "Hello {$user->name},\n\nYour admin password reset code is: {$code}\n\nThis code will expire in 60 minutes. If you did not request a password reset, please ignore this message.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Password Reset Code — ' . config('app.name', 'Portfolio Admin'));
                }
            );
        } catch (\Throwable $e) {
            report($e);
        }

        $includeCode = config('app.debug') || config('mail.default') === 'log';

        return response()->json([
            'success' => true,
            'message' => 'Password reset code has been sent to your email.',
            'data' => [
                'email' => $user->email,
                'reset_code' => $includeCode ? $code : null,
            ],
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We could not find an account with that email address.'],
            ]);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'code' => ['No active password reset request found for this email.'],
            ]);
        }

        $expiresAt = Carbon::parse($record->created_at)->addMinutes(60);
        if (now()->isAfter($expiresAt)) {
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            throw ValidationException::withMessages([
                'code' => ['This reset code has expired. Please request a new one.'],
            ]);
        }

        if (!Hash::check($request->code, $record->token) && $request->code !== $record->token) {
            throw ValidationException::withMessages([
                'code' => ['The provided reset code is invalid.'],
            ]);
        }

        $user->password = $request->password;
        $user->save();

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Revoke all existing tokens
        $user->tokens()->delete();

        // Create new token so the client can automatically authenticate if desired
        $newToken = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now use your new password.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $newToken,
            ],
        ]);
    }
}
