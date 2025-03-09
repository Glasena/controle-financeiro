<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCardTransaction extends Model
{
    protected $fillable = [
        'credit_card_name',
        'transaction_date',
        'description',
        'amount',
        'installments_total',
        'installment_number',
        'due_date',
        'paid',
        'transaction_id'
    ];

    // Se você quiser relacionar o pagamento com a transação na tabela transactions
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'credit_card_transaction_id');
    }
}
