<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMessageToSrpRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('srp_requests', function (Blueprint $table) {
            $table->after('reimbursement', function (Blueprint $table) {
                $table->mediumText('message')->default('');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('srp_requests', function (Blueprint $table) {
            //
        });
    }
}
