<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
            $table->string('method_name');
            $table->decimal('base_rate', 12, 2)->default(0);
            $table->decimal('min_weight', 8, 2)->nullable();
            $table->decimal('max_weight', 8, 2)->nullable();
            $table->decimal('extra_rate', 12, 2)->nullable();
            $table->decimal('free_shipping_min_amount', 12, 2)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
