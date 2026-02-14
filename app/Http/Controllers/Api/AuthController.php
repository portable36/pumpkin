<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\RefreshToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !\Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // create access token via Sanctum
        $accessToken = $user->createToken('api')->plainTextToken;

        // create refresh token (rotate)
        $plainRefresh = Str::random(64);
        $hash = hash('sha256', $plainRefresh);

        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => $hash,
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 512),
            'expires_at' => now()->addDays(30),
        ]);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $plainRefresh,
            'token_type' => 'Bearer',
        ]);
    }

    public function refresh(Request $request)
    {
        $data = $request->validate(['refresh_token' => 'required|string']);
        $hash = hash('sha256', $data['refresh_token']);

        $rt = RefreshToken::where('token_hash', $hash)->first();
        if (!$rt || ($rt->expires_at && $rt->expires_at->isPast())) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        $user = $rt->user;
        if (!$user) {
            return response()->json(['message' => 'Invalid token user'], 401);
        }

        // rotate: delete old refresh token, create new one
        $rt->delete();
        $newRefreshPlain = Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $newRefreshPlain),
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 512),
            'expires_at' => now()->addDays(30),
        ]);

        $accessToken = $user->createToken('api')->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshPlain,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            // Revoke current access token
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }

    // socialLogin stub - Socialite integration should be configured separately
    public function socialLogin(Request $request)
    {
        return response()->json(['message' => 'Social login handled elsewhere'], 501);
    }
}
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
