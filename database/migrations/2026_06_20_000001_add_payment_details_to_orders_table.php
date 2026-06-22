<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_status');
            }
            if (! Schema::hasColumn('orders', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('payment_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_notes')) {
                $table->dropColumn('payment_notes');
            }
            if (Schema::hasColumn('orders', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
        });
    }
};
