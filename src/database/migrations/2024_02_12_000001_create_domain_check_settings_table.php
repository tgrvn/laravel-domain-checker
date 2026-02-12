<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('domain_check_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->unique()->constrained()->onDelete('cascade');
            $table->integer('check_interval_minutes')->default(5);
            $table->integer('request_timeout_seconds')->default(10);
            $table->string('check_method')->default('GET');
            $table->boolean('auto_checks_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_check_settings');
    }
};
