<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Repositories\Client\ClientInterface;
use Ixudra\Curl\Facades\Curl;

class AlterTableClientsAddContactFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedTinyInteger('type')->default(0)->after('email');
            $table->string('country_code', 2)->after('type')->default("")->nullable();
            $table->string('city', 255)->after('country_code')->default("")->nullable();
            $table->string('address', 255)->after('city')->default("")->nullable();
            $table->unsignedInteger('postal_code')->after('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
