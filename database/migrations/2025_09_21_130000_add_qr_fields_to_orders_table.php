<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('qr_token')->nullable()->unique()->after('total');
            $table->timestamp('qr_scanned_at')->nullable()->after('qr_token');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_scanned_at']);
        });
    }
};
