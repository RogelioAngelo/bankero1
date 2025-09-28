<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('transactions', 'paymongo_payment_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('paymongo_payment_id')->nullable()->index();
                $table->string('payment_intent_id')->nullable()->index();
                $table->string('paymongo_source_id')->nullable()->index();
            });
        }
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['paymongo_payment_id', 'payment_intent_id', 'paymongo_source_id']);
        });
    }
};
