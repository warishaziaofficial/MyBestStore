<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_relations', function (Blueprint $table) {
            $table->id();
            $table->string('product_slug')->index();
            $table->string('related_product_slug')->index();
            $table->enum('relation_type', ['upsell', 'cross_sell', 'related']);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(
                ['product_slug', 'related_product_slug', 'relation_type'],
                'product_relations_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_relations');
    }
};
