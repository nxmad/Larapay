<?php

use Nxmad\Larapay\Models\Transaction;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * @var Builder
     */
    public $builder;

    /**
     * CreateTransactionsTable constructor.
     */
    public function __construct()
    {
        $this->builder = app(Builder::class);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->builder->create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->decimal('amount');

            $table->json('meta')->nullable();

            $table->enum('state', Transaction::STATES);

            $table->unsignedInteger('subject_id');

            $table->string('subject_type');

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
        $this->builder->dropIfExists('transactions');
    }
}
