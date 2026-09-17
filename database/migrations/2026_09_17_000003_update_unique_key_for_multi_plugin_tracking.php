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
            // Drop single site_id unique constraint
            $table->dropUnique('tracked_plugins_site_id_unique');

            // Add composite unique constraint for site_id + plugin_name
            $table->unique(['site_id', 'plugin_name'], 'tracked_plugins_site_plugin_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracked_plugins', function (Blueprint $table) {
            $table->dropUnique('tracked_plugins_site_plugin_unique');
            $table->unique('site_id', 'tracked_plugins_site_id_unique');
        });
    }
};
