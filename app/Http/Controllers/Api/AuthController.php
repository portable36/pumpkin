<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends \App\Http\Controllers\Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'required|string|unique:users,phone',
            'terms_accepted' => 'required|accepted',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        // Assign customer role
        $user->assignRole('customer');

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_id' => 'nullable|string',
        ]);

        // Check rate limit
        if (!$this->authService->checkRateLimit($request->email)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again later.',
            ], 429);
        }

        $user = $this->authService->authenticate(
            $request->email,
            $request->password,
            $request->device_id
        );

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }

    /**
     * Request OTP
     */
    public function requestOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'type' => 'required|in:email,phone',
        ]);

        $user = User::where('email', $request->email)->first();
        $otp = $this->authService->generateOTP($user, $request->type);

        // Send OTP via email or SMS
        // This would integrate with Mail or SMS service

        return response()->json([
            'message' => 'OTP sent successfully',
            'expires_in' => 600, // 10 minutes
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'type' => 'required|in:email,phone',
        ]);

        $user = User::where('email', $request->email)->first();
        $verified = $this->authService->verifyOTP($user, $request->otp, $request->type);

        if (!$verified) {
            return response()->json([
                'message' => 'Invalid or expired OTP',
            ], 401);
        }

        return response()->json([
            'message' => 'OTP verified successfully',
            'verified' => true,
        ]);
    }

    /**
     * Social login (Google/Facebook)
     */
    public function socialLogin(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:google,facebook',
            'token' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        // Verify token with provider and get user data
        // This is a simplified example

        $userData = $this->verifySocialToken($request->provider, $request->token);

        if (!$userData) {
            return response()->json([
                'message' => 'Invalid social token',
            ], 401);
        }

        $user = User::where("{$request->provider}_id", $userData['id'])->first();

        if (!$user) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make(\Illuminate\Support\Str::random(32)),
                "{$request->provider}_id" => $userData['id'],
                'email_verified_at' => now(),
            ]);

            $user->assignRole('customer');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Social login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Verify social token
     */
    private function verifySocialToken(string $provider, string $token): ?array
    {
        // Implementation would verify with Google/Facebook API
        return null;
    }
}
