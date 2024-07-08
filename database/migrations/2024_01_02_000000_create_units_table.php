<?php

use App\Enums\UnitType;
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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->string('short_name',10);
            $table->foreignId('unit_category_id')->constrained()->onDelete('cascade');
            $table->enum('unit_type', array_column(UnitType::cases(), 'value'))->default(UnitType::Reference->value);
            $table->decimal('value',22,4)->default(1.0000);
            $table->decimal('rounded_amount', 22,4)->default(4.0000);
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
        Schema::dropIfExists('units');
    }
};
