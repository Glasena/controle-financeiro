<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditCardTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('credit_card_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('credit_card_name'); // Ex: Nubank, Visa XP etc.
            $table->date('transaction_date');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            // Para compras parceladas
            $table->integer('installments_total')->nullable();
            $table->integer('installment_number')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('paid')->default(false);
            // Se você quiser relacionar o pagamento com a transação de cartão
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_card_transactions');
    }
}
