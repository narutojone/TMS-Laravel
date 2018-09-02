<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Carbon\Carbon;

class SystemSettingGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_setting_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('group_key', 50)->unigue();
            $table->timestamps();
        });

        $name = 'Phone System';
        $now = Carbon::now();

        DB::table('system_setting_groups')->insert(
            [
                'name' => $name,
                'group_key' => str_slug($name),
                'created_at' => $now,
                'updated_at' => $now
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_setting_groups');
    }
}
