<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'name',
        'surname',
        'balance',
        'user_id',
        'status',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    
}
