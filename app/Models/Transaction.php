<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_date',
        'description',
        'amount',
        'bank',
        'type',
        'credit_card_transaction_id',
        'competence',
    ];

    // Caso queira relacionar com a transação do cartão
    public function creditCardTransaction()
    {
        return $this->belongsTo(CreditCardTransaction::class, 'credit_card_transaction_id');
    }
}
