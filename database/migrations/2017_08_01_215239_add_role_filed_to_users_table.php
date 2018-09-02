<?php

use App\Repositories\Invitation\Invitation;
use App\Repositories\User\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleFiledToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('users', function (Blueprint $table) {
			$table->smallInteger('role')->unsigned()->after('admin')->nullable()->default(User::ROLE_EMPLOYEE);
		});

		Schema::table('invitations', function (Blueprint $table) {
			$table->smallInteger('role')->unsigned()->after('admin')->nullable()->default(User::ROLE_EMPLOYEE);
		});

		// Update roles for existing users (by default all are employees)
		User::where('admin', 1)->update(['role'=>User::ROLE_ADMIN]); // we will deprecate 'admin' field later on

		// Update roles for existing active invitations
		Invitation::where('admin', 1)->update(['role'=>User::ROLE_ADMIN]); // we will deprecate 'admin' field later on
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
