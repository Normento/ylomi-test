<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VerificationController extends Controller
{
    /**
     * Check verification code
     */
    public function checkcode(Request $request)
    {
        // Valider les données de la requête
        $request->validate([
            'code' => 'required|numeric',
        ]);

        try {
            $user = User::where('verification_code', $request->code)->first();

            if ($user) {
                // Vérifier si le code de vérification correspond
                if ($user->verification_code == $request->code) {
                    // Vérifier si le code de vérification n'a pas expiré
                    if ($user->updated_at->addMinutes(10) > now()) {
                        // Rendre le code de vérification nul et activer le compte
                        $user->verification_code = null;
                        $user->is_active = true;
                        $user->save();

                        return response()->json([
                            'status' => true,
                            'message' => 'Utilisateur connecté avec succès',
                            'user' => $user,
                            'token' => $user->createToken("API TOKEN")->plainTextToken
                        ], 200);
                    } else {
                        return response()->json(['message' => 'Le code de vérification est expiré'], 422);
                    }
                }
            }

            return response()->json(['message' => 'Code de vérification invalide'], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
