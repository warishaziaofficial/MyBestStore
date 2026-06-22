<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('shipping_amount');
            }
            if (! Schema::hasColumn('orders', 'shipping_zone')) {
                $table->string('shipping_zone')->nullable()->after('shipping_method');
            }
            if (! Schema::hasColumn('orders', 'total_weight')) {
                $table->decimal('total_weight', 8, 2)->default(0)->after('shipping_zone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('orders', 'shipping_method') ? 'shipping_method' : null,
                Schema::hasColumn('orders', 'shipping_zone') ? 'shipping_zone' : null,
                Schema::hasColumn('orders', 'total_weight') ? 'total_weight' : null,
            ]));
        });
    }
};
