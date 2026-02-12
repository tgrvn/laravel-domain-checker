<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->boolean('last_check_success')->nullable()->after('domain');
            $table->timestamp('last_checked_at')->nullable()->after('last_check_success');
        });
    }

    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['last_check_success', 'last_checked_at']);
        });
    }
};
