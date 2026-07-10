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
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->string('fulfillment_status')->nullable()->after('status');
            $table->string('fulfillment_method')->nullable()->after('fulfillment_status');
            $table->text('delivery_address')->nullable()->after('fulfillment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->dropColumn(['fulfillment_status', 'fulfillment_method', 'delivery_address']);
        });
    }
};
