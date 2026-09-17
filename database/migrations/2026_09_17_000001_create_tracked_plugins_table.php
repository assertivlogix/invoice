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
        Schema::create('tracked_plugins', function (Blueprint $table) {
            $table->id();
            $table->string('site_id')->unique();
            $table->string('site_url');
            $table->string('plugin_name')->default('assertivlogix-local-currency-display-woocommerce');
            $table->string('plugin_version');
            $table->string('wordpress_version');
            $table->string('php_version');
            $table->string('woocommerce_version')->nullable();
            $table->timestamp('last_seen')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['last_seen', 'plugin_version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracked_plugins');
    }
};
