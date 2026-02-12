<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemoteIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('remote_incomes', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('income_id')->index();
            $table->string('number', 64)->nullable()->index();

            $table->date('date')->index();
            $table->date('last_change_date')->nullable()->index();

            $table->string('supplier_article', 64)->nullable()->index();
            $table->string('tech_size', 64)->nullable();

            $table->bigInteger('barcode')->nullable()->index();

            $table->integer('quantity')->default(0);

            $table->decimal('total_price', 14, 2)->default(0);

            $table->date('date_close')->nullable()->index();

            $table->string('warehouse_name', 255)->nullable()->index();

            $table->bigInteger('nm_id')->index();

            $table->timestamps();

			// to avoid creating duplicates:
            $table->unique(
                array('income_id', 'nm_id', 'barcode', 'date'),
                'remote_incomes_unique_income_nm_barcode_date'
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
        Schema::dropIfExists('remote_incomes');
    }
}
