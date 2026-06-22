<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courier_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_companies');
    }
};
