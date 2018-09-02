<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Repositories\ContactEmail\ContactEmailInterface;

class AlterContactsAddZendeskId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_emails', function (Blueprint $table) {
            $table->string('zendesk_id', 20)->after('address')->nullable();
        });

        $errorLog = [];
        $contactEmailsRepository = app()->make(ContactEmailInterface::class);

        $contactsEmails = $contactEmailsRepository->all();
        foreach ($contactsEmails as $contactEmail) {

            $zendeskUser = DB::table('zendesk_users')->where('email', '=', $contactEmail->address)->first();
            if(!$zendeskUser) {
                $errorLog['not-found'][] = $contactEmail->address;
                continue;
            }

            $contactEmail->update([
                'zendesk_id' => $zendeskUser->zendesk_id,
            ]);
        }

//        dd($errorLog);
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
