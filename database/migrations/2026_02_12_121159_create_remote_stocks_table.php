<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemoteStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
    {
        Schema::create('remote_stocks', function (Blueprint $table) {
            $table->id();

            $table->date('date')->index();
            $table->date('last_change_date')->nullable()->index();

            $table->string('supplier_article', 64)->nullable();
            $table->string('tech_size', 64)->nullable();

            $table->bigInteger('barcode')->nullable()->index();

            $table->integer('quantity')->default(0);
            $table->boolean('is_supply')->nullable()->index();
            $table->boolean('is_realization')->nullable()->index();
            $table->integer('quantity_full')->nullable();

            $table->string('warehouse_name', 255)->nullable()->index();

            $table->integer('in_way_to_client')->nullable();
            $table->integer('in_way_from_client')->nullable();

            $table->bigInteger('nm_id')->index();

            $table->string('subject', 64)->nullable()->index();
            $table->string('category', 64)->nullable()->index();
            $table->string('brand', 64)->nullable()->index();

            $table->bigInteger('sc_code')->nullable()->index();

            $table->decimal('price', 14, 2)->default(0);
            $table->unsignedSmallInteger('discount')->default(0);

            $table->timestamps();

			// To avoid duplicating entries per day:
			 // one position (nm_id + barcode + warehouse + date) = unique
			$table->unique(
                array('date', 'warehouse_name', 'nm_id', 'barcode'),
                'remote_stocks_unique_day_wh_nm_barcode'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remote_stocks');
    }
}
