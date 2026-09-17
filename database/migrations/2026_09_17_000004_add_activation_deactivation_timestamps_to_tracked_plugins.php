<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tracked_plugins', function (Blueprint $table) {
            $table->string('status', 30)->default('active')->after('woocommerce_version');
            $table->timestamp('activated_at')->nullable()->after('status');
            $table->timestamp('deactivated_at')->nullable()->after('activated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracked_plugins', function (Blueprint $table) {
            $table->dropColumn(['status', 'activated_at', 'deactivated_at']);
        });
    }
};
