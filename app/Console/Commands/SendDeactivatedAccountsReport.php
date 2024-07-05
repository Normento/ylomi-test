<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BankAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeactivatedAccountsReport;

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
            Mail::to($admin->email)->send(new DeactivatedAccountsReport($deactivatedAccounts));
        }

        $this->info('Rapport des comptes désactivés envoyé aux administrateurs.');
    }
    }

