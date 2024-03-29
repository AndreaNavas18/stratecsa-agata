<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets_visits_files', function (Blueprint $table) {
            //
            $table->bigInteger('employee_file_id')->nullable()->after('ticket_visit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets_visits_files', function (Blueprint $table) {
            //
            $table->dropColumn('employee_file_id');
        });
    }
};
