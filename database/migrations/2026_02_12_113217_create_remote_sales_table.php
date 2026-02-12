<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemoteSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('remote_sales', function (Blueprint $table) {
            $table->id();

            $table->string('g_number', 32)->index();

            // в JSON дата без времени
            $table->date('date')->index();
            $table->date('last_change_date')->index();

            $table->string('supplier_article', 64)->index();
            $table->string('tech_size', 64)->nullable();

            $table->bigInteger('barcode')->nullable()->index();

            $table->decimal('total_price', 14, 2)->default(0);
            $table->unsignedSmallInteger('discount_percent')->default(0);

            $table->boolean('is_supply')->default(false)->index();
            $table->boolean('is_realization')->default(false)->index();

            $table->decimal('promo_code_discount', 14, 2)->nullable();

            $table->string('warehouse_name', 255)->nullable()->index();
            $table->string('country_name', 255)->nullable()->index();
            $table->string('oblast_okrug_name', 255)->nullable()->index();
            $table->string('region_name', 255)->nullable()->index();

            $table->bigInteger('income_id')->default(0)->index();

            $table->string('sale_id', 32)->nullable()->index();

            $table->string('odid', 64)->nullable()->index();

            $table->unsignedSmallInteger('spp')->default(0);

            $table->decimal('for_pay', 14, 2)->default(0);
            $table->decimal('finished_price', 14, 2)->default(0);
            $table->decimal('price_with_disc', 14, 2)->default(0);

            $table->bigInteger('nm_id')->index();

            $table->string('subject', 64)->nullable()->index();
            $table->string('category', 64)->nullable()->index();
            $table->string('brand', 64)->nullable()->index();

            $table->boolean('is_storno')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remote_sales');
    }
}
