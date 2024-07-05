<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\MailObject;
use App\Mail\SendVerificationCode;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\RegisterRequest;
use App\Notifications\NotificationService;
use Laravel\Fortify\Contracts\RegisterResponse;

class RegisteredUserController extends Controller
{
    /**
     * Register.
     */
    public function register(RegisterRequest $request)
    {
        // Créer un nouvel utilisateur
        $verificationCode = rand(100000, 999999);
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
            'is_active' => false,
        ]);

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

        // Retourner une réponse JSON
        return response()->json([
            'status' => true,
            'message' => 'Inscription réussie. Un code de vérification a été envoyé à votre e-mail.'
        ], 201);
    }
}

