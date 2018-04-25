<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrackerUserAgentFieldToTrackerSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::connection('tracker')->table('tracker_sessions', function (Blueprint $table) {
            $table->string('user_agent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tracker')->table('tracker_sessions', function (Blueprint $table) {
            $table->dropColumn(['user_agent']);
        });
    }
}