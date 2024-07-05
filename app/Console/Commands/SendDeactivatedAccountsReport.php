<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Console\Command;
use App\Notifications\MailObject;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeactivatedAccountsReport;
use App\Notifications\NotificationService;

class SendDeactivatedAccountsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:deactivated-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie un rapport des comptes désactivés aux administrateurs deux jours avant la fin du mois';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deactivatedAccounts = BankAccount::where('status', 'disabled')
                                          ->whereMonth('updated_at', Carbon::now()->month)
                                          ->get();

        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {

            $userMail[] = $admin->email;
            (new NotificationService)->toEmails($userMail)->sendMail(
                new MailObject(
                    subject: 'Rapport des comptes désactivés du mois',
                    title: 'Rapport des comptes désactivés du mois',
                    // intro: 'Voici votre code de réinitialisation',
                    corpus: 'Rapport des comptes désactivés du mois',
                    outro: "Rapport des comptes désactivés du mois",
                    template: 'emails.deactivated_accounts_report',
                    data: [
                        "reports" => $deactivatedAccounts,
                        "nom" => $admin->name,
                    ],

                )
            );
        }

        $this->info('Rapport des comptes désactivés envoyé aux administrateurs.');
    }
    }

