<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Carbon\Carbon;

class SystemSettingValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_setting_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_id')->nullable();
            $table->string('setting_key', 50);
            $table->string('input_type', 20)->index();
            $table->string('name', 50)->unique();
            $table->string('validation_rule')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();

            $table->unique(['group_id', 'setting_key']);

            $table->foreign('group_id')
                ->references('id')->on('system_setting_groups')
                ->onDelete('set null');
        });

        $fallback = 'Fallback number';
        $greeting = 'Greeting message';
        $appError = 'Application error';
        $afterHours = 'After hours';
        $now = Carbon::now();

        DB::table('system_setting_values')->insert([
                [
                    'group_id' => 1,
                    'name' => $fallback,
                    'input_type' => 'number',
                    'validation_rule' => 'required|integer',
                    'setting_key' => str_slug($fallback),
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'group_id' => 1,
                    'name' => $greeting,
                    'input_type' => 'file',
                    'validation_rule' => 'file|mimetypes:audio/mpeg',
                    'setting_key' => str_slug($greeting),
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'group_id' => 1,
                    'name' => $appError,
                    'input_type' => 'file',
                    'validation_rule' => 'file|mimetypes:audio/mpeg',
                    'setting_key' => str_slug($appError),
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'group_id' => 1,
                    'name' => $afterHours,
                    'input_type' => 'file',
                    'validation_rule' => 'file|mimetypes:audio/mpeg',
                    'setting_key' => str_slug($afterHours),
                    'created_at' => $now,
                    'updated_at' => $now
                ]
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
        Schema::dropIfExists('system_setting_values');
    }
}
