<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Users listes
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::all();

        return response()->json([
            'status' => true,
            'data' => $users
        ], 200);
    }

    /**
     * update user
     */
    public function update(Request $request, $userId)
    {
        $this->authorize('update', User::class);

        $user = User::findOrFail($userId);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
            'is_admin' => 'sometimes|required|boolean',
        ]);

        $user->update($request->only(['name', 'email', 'is_admin']));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => $user
        ], 200);
    }

    /**
     * delete user
     */
    public function destroy($userId)
    {
        $this->authorize('delete', User::class);

        $user = User::findOrFail($userId);
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Utilisateur supprimé avec succès'
        ], 200);
    }
}
