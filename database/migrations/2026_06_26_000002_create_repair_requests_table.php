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
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->unique();

            // Customer who submitted the request.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Technician assigned to the request (set later by an admin).
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('device_type');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('issue_description');

            $table->string('priority')->default('medium');
            $table->string('status')->default('pending');

            $table->string('image_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_requests');
    }
};
