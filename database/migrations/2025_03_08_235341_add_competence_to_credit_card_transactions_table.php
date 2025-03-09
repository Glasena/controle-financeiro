<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompetenceToCreditCardTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('credit_card_transactions', function (Blueprint $table) {
            $table->string('competence')->nullable()->after('credit_card_name');
        });
    }

    public function down()
    {
        Schema::table('credit_card_transactions', function (Blueprint $table) {
            $table->dropColumn('competence');
        });
    }
}
