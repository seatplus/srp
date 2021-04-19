<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSrpRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('srp_requests', function (Blueprint $table) {
            $table->string('id');
            $table->foreignId('user_id')->constrained('users');
            $table->json('killmail');
            $table->text('description')->default('');
            $table->enum('status', ['open', 'accepted', 'denied'])->default('open');
            $table->double('amount')->nullable();
            $table->timestamps();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srp_requests');
    }
}
