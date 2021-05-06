<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSrpReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('srp_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiver_id')->constrained('users');
            $table->foreignId('accountant_id')->constrained('users');
            $table->string('uuid');
            $table->double('amount')->default(0.0);
            $table->timestamps();

            $table->index('uuid');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srp_receipts');
    }
}
