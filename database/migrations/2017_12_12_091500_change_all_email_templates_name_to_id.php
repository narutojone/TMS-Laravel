<?php

use App\Repositories\EmailTemplate\EmailTemplate;
use App\Repositories\TemplateNotification\TemplateNotification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAllEmailTemplatesNameToId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TemplateNotification::where('type', 'template')->get()->each(function ($notification) {
            if ($emailTemplate = EmailTemplate::template($notification->details['template'])->first()) {
                $data = $notification->details;
                $data['template'] = $emailTemplate->id;
                $notification->details = $data;
                $notification->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        TemplateNotification::where('type', 'template')->get()->each(function ($notification) {
            if ($emailTemplate = EmailTemplate::find($notification->details['template'])) {
                $data = $notification->details;
                $data['template'] = $emailTemplate->name;
                $notification->details = $data;
                $notification->save();
            }
        });
    }
}
