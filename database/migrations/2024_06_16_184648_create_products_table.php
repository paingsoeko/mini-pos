<?php

use App\Enums\Product\Type;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 255)->unique()->nullable();
            $table->string('product_code', 255)->nullable();
            $table->string('name', 255);
            $table->string('image')->nullable();
            $table->enum('type',array_column(Type::cases(), 'value'))->default(Type::storable);
            $table->decimal('sales_price', 22,4)->default(0.0000);
            $table->decimal('cost', 22,4)->default(0.0000);
            $table->foreignId('product_category_id')->nullable();
            $table->bigInteger('unit_id');
            $table->bigInteger('purchase_unit_id');
            $table->integer('security_stock')->nullable();
            $table->text('internal_notes')->nullable();
            $table->boolean('can_be_sold')->default(true);
            $table->boolean('can_be_purchased')->default(true);
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
        Schema::dropIfExists('products');
    }
};
