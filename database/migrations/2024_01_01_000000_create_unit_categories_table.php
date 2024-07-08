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
            Schema::create('unit_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50);
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by')->nullable();
                $table->bigInteger('deleted_by')->nullable();
                $table->dateTime('deleted_at')->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_categories');
    }
};
