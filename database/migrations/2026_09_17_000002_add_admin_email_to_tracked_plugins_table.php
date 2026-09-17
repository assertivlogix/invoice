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
            $table->string('admin_email')->nullable()->after('site_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracked_plugins', function (Blueprint $table) {
            $table->dropColumn('admin_email');
        });
    }
};
