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

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('hsn_code');
            $table->string('name');
            $table->string('medicine_name')->nullable();
            $table->string('slug')->unique();
            $table->string('generic_name')->nullable();
            $table->string('manufature')->nullable();
            $table->string('batch_no')->nullable();
            $table->decimal('gst', 5, 2)->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->string('video_url')->nullable();
            $table->date('expiration_date')->nullable();
            $table->boolean('prescription_required')->default(false);
            $table->text('description')->nullable();
            $table->json('categories')->nullable();
            $table->decimal('shipping_charge', 8, 2)->nullable();
            $table->string('tax_class')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('price', 10, 2);
            $table->decimal('mrp_price', 10, 2);
            $table->decimal('special_price', 10, 2)->nullable();
            $table->decimal('special_start_price', 10, 2)->nullable();
            $table->decimal('special_end_price', 10, 2)->nullable();
            $table->string('sku')->nullable();
            $table->boolean('inventory_manage')->default(true);
            $table->integer('qty')->default(0);
            $table->boolean('stock_manage')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->string('meta_description')->nullable();
            $table->json('related_product')->nullable();
            $table->json('up_sell')->nullable();
            $table->json('cross_sell')->nullable();
            $table->text('short_description')->nullable();
            $table->date('product_new_from')->nullable();
            $table->date('product_new_to')->nullable();
            $table->integer('like_count')->default(0);
            $table->string('unit')->nullable();
            $table->integer('package_per_unit')->nullable();
            $table->decimal('price_per_unit', 8, 2)->nullable();
            $table->string('product_form')->nullable();
            $table->string('cat')->nullable();
            $table->string('test_id')->nullable();
            $table->boolean('is_drug')->default(false);
            $table->string('pack_size_label')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
