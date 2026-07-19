<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->decimal('quote_service_charge', 10, 2)->nullable()->after('diagnosis_notes');
            $table->decimal('quote_parts_cost', 10, 2)->nullable()->after('quote_service_charge');
            $table->decimal('quote_discount', 10, 2)->nullable()->after('quote_parts_cost');
            $table->decimal('diagnosis_fee', 10, 2)->nullable()->after('quote_discount');
            $table->text('quote_notes')->nullable()->after('diagnosis_fee');
            $table->timestamp('quoted_at')->nullable()->after('quote_notes');
            $table->timestamp('quote_responded_at')->nullable()->after('quoted_at');
        });
    }

    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->dropColumn([
                'quote_service_charge',
                'quote_parts_cost',
                'quote_discount',
                'diagnosis_fee',
                'quote_notes',
                'quoted_at',
                'quote_responded_at',
            ]);
        });
    }
};
