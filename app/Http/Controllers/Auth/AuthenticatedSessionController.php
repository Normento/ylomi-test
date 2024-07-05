<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Notifications\MailObject;
use App\Mail\SendVerificationCode;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\NotificationService;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\FailedLoginResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Login
     */
    public function store(LoginRequest $request)
    {
        try {
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'L\'email et le mot de passe ne correspondent pas à nos enregistrements.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            if ($user->is_active !== 1) {
                $verificationCode = mt_rand(100000, 999999);
                $user->verification_code = $verificationCode;
                $user->save();

                // Envoyer le code de vérification par e-mail
                $userMail[] = $user->email;
                (new NotificationService)->toEmails($userMail)->sendMail(
                    new MailObject(
                        subject: 'Code d"authentification de votre compte',
                        title: 'Code d"authentification de votre compte',
                        // intro: 'Voici votre code de réinitialisation',
                        corpus: $verificationCode,
                        outro: "Merci de nous aidez à sécuriser votre compte",
                        template: 'emails.verification_code',
                        data: [
                            "code" => $verificationCode,
                            'nom' => $user->nom
                        ],

                    )
                );

                return response()->json(['message' => 'Votre compte n\'est pas activé. Un code de vérification a été envoyé à votre e-mail.'], 403);
            }

            // Si le compte est activé, créer un token API
            return response()->json([
                'status' => true,
                'message' => 'Utilisateur connecté avec succès',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }



    /**
     * Logout
     */
    public function logout()
    {
        // Supprimer tous les tokens de l'utilisateur
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Vous vous êtes déconnecté avec succès et le token a été supprimé avec succès'
        ], 200);
    }


}
