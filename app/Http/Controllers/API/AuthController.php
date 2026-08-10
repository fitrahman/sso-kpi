<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PassportClient;
use App\Models\User;
use App\Models\UserClientRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Passport\RefreshTokenRepository;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'confirmed'],
                'phone' => 'nullable|string|max:15',
                'role' => 'required|in:'.implode(',', User::ROLES),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Registration successful! Waiting for approval.',
                'user' => $user,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            if (! Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $user = Auth::user();
            if ($user->status === 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda masih menunggu persetujuan dari Administrator.',
                ], 403);
            }

            $token = $user->createToken('API Token')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user details
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user();
            $clientId = $request->query('client_id');

            $role = 'user';
            $hasAccess = false;

            if ($user->role === 'admin') {
                $hasAccess = true;
            }

            if ($clientId) {
                $clientRole = UserClientRole::where('user_id', $user->id)
                    ->where('oauth_client_id', $clientId)
                    ->first();

                if ($clientRole && ! empty($clientRole->role)) {
                    $role = $clientRole->role;
                } else {
                    $role = $user->role ?? 'user';
                }

                if ($user->role !== 'admin') {
                    $accessObj = DB::table('client_user_access')
                        ->where('user_id', $user->id)
                        ->where('client_id', $clientId)
                        ->first();
                    $hasAccess = $accessObj ? (bool) $accessObj->is_active : false;
                }
            } else {
                $role = $user->role ?? 'user';
                if ($user->role !== 'admin') {
                    // If no client_id, check if user is active/approved globally
                    $hasAccess = $user->status === 'approved';
                }
            }

            // Target response format requested by the user
            $responsePayload = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
                'has_access' => $hasAccess,
            ];

            // Backward compatibility wrapper for SocialiteProviders (expects data.user structure)
            $responsePayload['success'] = true;
            $responsePayload['data'] = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $role,
                    'has_access' => $hasAccess,
                ],
            ];

            return response()->json($responsePayload, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get supported roles for a client application
     */
    public function getClientRoles(Request $request)
    {
        $clientId = $request->query('client_id');
        if (! $clientId) {
            return response()->json(['success' => false, 'message' => 'client_id parameter is required'], 400);
        }

        $client = PassportClient::find($clientId);
        if (! $client) {
            return response()->json(['success' => false, 'message' => 'Client application not found'], 404);
        }

        $supportedRoles = json_decode($client->supported_roles, true) ?? [];

        return response()->json([
            'success' => true,
            'client_id' => $client->id,
            'client_name' => $client->name,
            'supported_roles' => $supportedRoles,
        ]);
    }

    /**
     * Sync/Update supported roles for a client application via API
     */
    public function syncClientRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer',
            'client_secret' => 'required|string',
            'roles' => 'required|array',
            'roles.*' => 'string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $client = PassportClient::find($request->client_id);
        if (! $client) {
            return response()->json(['success' => false, 'message' => 'Client application not found'], 404);
        }

        // Always verify client_secret
        if ($client->secret !== $request->client_secret) {
            return response()->json(['success' => false, 'message' => 'Invalid client secret'], 401);
        }

        $rolesArray = array_values(array_unique(array_map('trim', $request->roles)));
        $client->supported_roles = json_encode($rolesArray);
        $client->save();

        return response()->json([
            'success' => true,
            'message' => "Roles for '{$client->name}' synchronized successfully.",
            'client_id' => $client->id,
            'supported_roles' => $rolesArray,
        ]);
    }

    /**
     * Logout user (Revoke token)
     */
    public function logout(Request $request)
    {
        try {
            $token = $request->user()->token();
            $token->revoke();

            // Also revoke refresh tokens
            $tokenRepository = app(RefreshTokenRepository::class);
            $tokenRepository->revokeRefreshTokensByAccessTokenId($token->id);

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
