<?php

namespace App\Models;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id',
        'type',
        'amount',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
