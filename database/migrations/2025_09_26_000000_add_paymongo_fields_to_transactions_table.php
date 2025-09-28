<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add columns only when they don't already exist (idempotent)
        if (!Schema::hasColumn('transactions', 'checkout_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('checkout_id')->nullable()->after('order_id');
            });
        }

        if (!Schema::hasColumn('transactions', 'paymongo_session_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('paymongo_session_id')->nullable()->after('checkout_id');
            });
        }

        if (!Schema::hasColumn('transactions', 'amount')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->decimal('amount', 10, 2)->nullable()->after('paymongo_session_id');
            });
        }

        // Add index if the column exists and index not present
        if (Schema::hasColumn('transactions', 'checkout_id')) {
            try {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->index('checkout_id');
                });
            } catch (\Exception $e) {
                // Index may already exist; ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('transactions', 'checkout_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                // Drop index if exists - using index name
                if (Schema::hasColumn('transactions', 'checkout_id')) {
                    try {
                        $table->dropIndex('transactions_checkout_id_index');
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            });
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'checkout_id')) {
                $table->dropColumn('checkout_id');
            }
            if (Schema::hasColumn('transactions', 'paymongo_session_id')) {
                $table->dropColumn('paymongo_session_id');
            }
            if (Schema::hasColumn('transactions', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }
};
