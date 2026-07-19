<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            if (Schema::hasColumn('repair_requests', 'document_original_name')) {
                $table->dropColumn('document_original_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            $table->string('document_original_name')->nullable()->after('image_path');
        });
    }
};
