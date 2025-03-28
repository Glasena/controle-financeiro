<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('bank');
            // Tipo de transação, para diferenciar, por exemplo, pagamento de fatura do cartão
            $table->enum('type', ['normal', 'credit_card_payment', 'other'])->default('normal');
            // Caso queira relacionar o pagamento do cartão com as transações do cartão, use essa FK (opcional)
            $table->unsignedBigInteger('credit_card_transaction_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
