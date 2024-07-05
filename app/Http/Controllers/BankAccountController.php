<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Notifications\MailObject;
use App\Notifications\NotificationService;
use App\Http\Requests\CreateAccountRequest;

/**
 * Create bank account
 */
class BankAccountController extends Controller
{
    // Fonction pour créer un nouveau compte bancaire
    public function store(Request $request)
    {
        $user = auth()->user();

        $accountNumber = strtoupper(Str::random(10));

        // Créer un nouveau compte bancaire
        $bankAccount = BankAccount::create([
            'account_number' => $accountNumber,
            'name' => $user->name,
            'surname' => $user->surname,
            'balance' => 0,  
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Compte bancaire créé avec succès',
            'data' => $bankAccount
        ], 201);
    }


    // Fonction pour faire un dépôt sur un compte bancaire

    /**
     * Make deposit
     */
    public function deposit(Request $request, $accountId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $bankAccount = BankAccount::where('account_number', $accountId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$bankAccount) {
            return response()->json(['message' => 'Compte bancaire non trouvé ou vous n\'avez pas accès à ce compte'], 404);
        }

        if ($bankAccount->status !== 'active') {
            return response()->json(['message' => 'Le compte bancaire n\'est pas actif'], 403);
        }

        $bankAccount->balance += $request->amount;
        $bankAccount->save();

        $transaction = Transaction::create([
            'bank_account_id' => $bankAccount->id,
            'type' => 'deposit',
            'amount' => $request->amount,
        ]);

        $userMail[] = auth()->user()->email;
        (new NotificationService)->toEmails($userMail)->sendMail(
            new MailObject(
                subject: 'Transaction effectue sur votre compte',
                title: 'Transaction effectue sur votre compte',
                // intro: 'Voici votre code de réinitialisation',
                corpus: 'Transaction effectue sur votre compte',
                outro: "Merci de nous aid",
                template: 'emails.transactions',
                data: [
                    "solde" => $bankAccount->balance,
                    'operation' => $transaction->type,
                    'montant' => $transaction->amount,
                    'nom' => auth()->user()->name
                ],

            )
        );

        return response()->json([
            'status' => true,
            'message' => 'Dépôt effectué avec succès',
            'data' => $bankAccount
        ], 200);
    }



    /**
     * Make withdraw
     */
    public function withdraw(Request $request, $accountId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $bankAccount = BankAccount::where('account_number', $accountId)
            ->where('user_id', auth()->id()) 
            ->first();

        if (!$bankAccount) {
            return response()->json(['message' => 'Compte bancaire non trouvé ou vous n\'avez pas accès à ce compte'], 404);
        }

        if ($bankAccount->status !== 'active') {
            return response()->json(['message' => 'Le compte bancaire n\'est pas actif'], 403);
        }

        if ($bankAccount->balance < $request->amount) {
            return response()->json(['message' => 'Solde insuffisant'], 403);
        }

        $bankAccount->balance -= $request->amount;
        $bankAccount->save();

        $transaction = Transaction::create([
            'bank_account_id' => $bankAccount->id,
            'type' => 'withdraw',
            'amount' => $request->amount,
        ]);

        $userMail[] = auth()->user()->email;
        (new NotificationService)->toEmails($userMail)->sendMail(
            new MailObject(
                subject: 'Transaction effectue sur votre compte',
                title: 'Transaction effectue sur votre compte',
                // intro: 'Voici votre code de réinitialisation',
                corpus: 'Transaction effectue sur votre compte',
                outro: "Merci de nous aid",
                template: 'emails.transactions',
                data: [
                    "solde" => $bankAccount->balance,
                    'operation' => $transaction->type,
                    'montant' => $transaction->amount,
                    'nom' => auth()->user()->name
                ],

            )
        );

        // Retourner une réponse JSON
        return response()->json([
            'status' => true,
            'message' => 'Retrait effectué avec succès',
            'data' => $bankAccount
        ], 200);
    }



    /**
     * find transactions
     */
    public function transactions(Request $request, $accountId)
    {
        $user = auth()->user();

        $bankAccount = BankAccount::where('account_number', $accountId)->first();

        if (!$bankAccount) {
            return response()->json(['message' => 'Compte bancaire non trouvé'], 404);
        }

        $this->authorize('viewTransactions', $bankAccount);

        $transactions = $bankAccount->transactions;

        if($transactions){
            return response()->json([
                'status' => true,
                'message' => 'Transactions récupérées avec succès',
                'data' => $transactions
            ], 200);

        }

        return response()->json([
            'status' => false,
            'message' => 'Aucune transactions trouvé',
            'data' => $transactions
        ], 200);

       
    }


    /**
     * desactivate account
     */
    public function deactivate(Request $request, $accountId)
    {
        $this->authorize('deactivate', BankAccount::class);

        $bankAccount = BankAccount::where('account_number', $accountId)->first();

        $bankAccount->status = 'disabled';
        $bankAccount->save();

        return response()->json([
            'status' => true,
            'message' => 'Compte bancaire désactivé avec succès',
            'data' => $bankAccount
        ], 200);
    }


    /**
     * activate account
     */
    public function activate(Request $request, $accountId)
    {
        $this->authorize('deactivate', BankAccount::class);

        $bankAccount = BankAccount::where('account_number', $accountId)->first();

        $bankAccount->status = 'active';
        $bankAccount->save();

        return response()->json([
            'status' => true,
            'message' => 'Compte bancaire activé avec succès',
            'data' => $bankAccount
        ], 200);
    }


    /**
     * View account
     */
    public function show(Request $request, $accountId)
    {
        $bankAccount = BankAccount::where('account_number', $accountId)->first();


        $this->authorize('view', $bankAccount);

        return response()->json([
            'status' => true,
            'message' => 'Détails du compte récupérés avec succès',
            'data' => $bankAccount
        ], 200);
    }


    /**
     * Listes account
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->is_admin) {
            $bankAccounts = BankAccount::all();
        } else {

            $bankAccounts = BankAccount::where('user_id', $user->id)->get();
        }

        return response()->json([
            'status' => true,
            'message' => 'Comptes bancaires récupérés avec succès',
            'data' => $bankAccounts
        ], 200);
    }


    /**
     * Listes desactivate account
     */
    public function listDeactivatedAccounts(Request $request)
    {
        $this->authorize('deactivate', BankAccount::class);

        $deactivatedAccounts = BankAccount::where('status', 'disabled')->get();

        if($deactivatedAccounts){

            return response()->json([
                'status' => true,
                'message' => 'Comptes bancaires désactivés récupérés avec succès',
                'data' => $deactivatedAccounts
            ], 200);

        }

        return response()->json([
            'status' => false,
            'message' => 'Aucun comptes bancaires désactivés trouvé',
        ], 404);

        
    }
}

