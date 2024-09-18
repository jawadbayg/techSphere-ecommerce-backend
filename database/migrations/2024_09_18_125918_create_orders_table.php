<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('product_unique_id');
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->string('status')->default('pending');
            $table->timestamps();

            // Index for faster lookups
            $table->index('product_unique_id');
            // Foreign key constraint
            $table->foreign('product_unique_id')
                ->references('product_unique_id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['product_unique_id']);
        });

        // Drop the table
        Schema::dropIfExists('orders');
    }
}
