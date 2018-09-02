<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\Template\Template;
use App\Repositories\Task\Task;
use App\Repositories\TemplateSubtask\TemplateSubtask;
use App\Repositories\Subtask\Subtask;

class MigrateWysiwyg extends Migration
{
    protected $tables = [
        'templates' => Template::class,
        'tasks' => Task::class,
        'template_subtasks' => TemplateSubtask::class,
        'subtasks' => Subtask::class,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $table => $model) {
            // Update the description column type
            DB::statement('ALTER TABLE ' . $table . ' MODIFY description LONGTEXT');

            // Update the rows
            $model::all()->each(function ($row) {
                $row->description = '{"ops":[{"insert":"' .
                    str_replace('"', '\"', $row->description) . '\n"}]}';

                $row->save();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $table => $model) {
            // Reverse the row changes
            $model::all()->each(function ($row) {
                $row->description = substr(substr($row->description, 19), 0, -6);
                $row->description = str_replace('\"', '"', $row->description);

                $row->save();
            });

            // Reverse the description column type change
            DB::statement('ALTER TABLE ' . $table . ' MODIFY description TEXT');
        }
    }
}
