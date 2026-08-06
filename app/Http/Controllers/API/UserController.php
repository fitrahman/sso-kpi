<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users (Admin only)
     */
    public function index(Request $request)
    {
        try {
            if (! $request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $users = User::paginate(20);

            return response()->json([
                'success' => true,
                'data' => $users,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get specific user
     */
    public function show(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Users can only see their own data, admins can see all
            if (! $request->user()->isAdmin() && $request->user()->id != $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
}
