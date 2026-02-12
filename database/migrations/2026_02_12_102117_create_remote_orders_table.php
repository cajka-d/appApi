<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemoteOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
 public function up(): void
    {
        Schema::create('remote_orders', function (Blueprint $table) {
            $table->id();

            $table->string('g_number', 32)->index();

            $table->dateTime('date')->index();

            $table->date('last_change_date')->index();

            $table->string('supplier_article', 64)->index();
            $table->string('tech_size', 64)->nullable();

            $table->bigInteger('barcode')->index();

            $table->decimal('total_price', 12, 2)->default(0);

            $table->unsignedSmallInteger('discount_percent')->default(0);

            $table->string('warehouse_name', 255)->nullable()->index();
            $table->string('oblast', 255)->nullable()->index();

            $table->unsignedBigInteger('income_id')->default(0)->index();

            $table->string('odid', 64)->nullable()->index();

            $table->bigInteger('nm_id')->index();

            $table->string('subject', 64)->nullable()->index();
            $table->string('category', 64)->nullable()->index();
            $table->string('brand', 64)->nullable()->index();

            $table->boolean('is_cancel')->default(false)->index();
            $table->dateTime('cancel_dt')->nullable()->index();

            $table->timestamps();

            // если g_number уникален в вашей системе — раскомментируйте
            // $table->unique('g_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remote_orders');
    }
}
