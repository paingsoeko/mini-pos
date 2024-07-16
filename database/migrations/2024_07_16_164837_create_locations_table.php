<?php

use App\Enums\Location\StockFlow;
use App\Enums\Location\Type;
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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('type',array_column(Type::cases(), 'value'))->nullable();
            $table->enum('stock_flow',array_column(StockFlow::cases(), 'value'))->nullable();
            $table->unsignedBigInteger('parent_location_id')->constrained('locations')->onDelete('RESTRICT')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
