<?php

use App\Repositories\Subtask\Subtask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('SubtasksTableSeeder');
        $this->call('TemplateFoldersTableSeeder');
        $this->call('EmailTemplatesTableSeeder');
        $this->call('OverdueReasonTableSeeder');
        $this->call('FixTaskOverdueReasonTableSeeder');

        $this->command->info('table seeded!');
    }
}

class SubtasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subtasks as st')
            ->whereNull('st.user_id')
            ->whereNotNull('st.completed_at')
            ->leftJoin('tasks as t', 'st.task_id', '=', 't.id')
            ->update([ 'st.user_id' => DB::raw("`t`.`user_id`") ]);
    }
}

class TemplateFoldersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('template_folders')->insert([
            ['name' => 'templates'],
        ]);
    }
}

class EmailTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_templates')->update([
            'folder_id' => 1,
        ]);
    }
}

class OverdueReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('overdue_reasons')->insert([
            ['reason' => 'Waiting on customer', 'required' => true, 'priority' => 1, 'visible' => true],
            ['reason' => 'Waiting on me', 'required' => true, 'priority' => 2, 'visible' => true],
            ['reason' => 'Problem with system', 'required' => true, 'priority' => 3, 'visible' => true],
            ['reason' => 'Other', 'required' => true, 'priority' => 4, 'visible' => true],
            ['reason' => 'Task should not be on client', 'required' => false, 'priority' => 5, 'visible' => true],
            ['reason' => 'No longer a client', 'required' => true, 'priority' => 6, 'visible' => true],
            ['reason' => 'Done by another employee', 'required' => true, 'priority' => 7, 'visible' => true],
        ]);
    }
}

class FixTaskOverdueReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::delete('DELETE tor FROM task_overdue_reasons tor
            LEFT JOIN (SELECT MIN(id) AS `id` FROM task_overdue_reasons GROUP BY `task_id`, `user_id`, `reason_id`, `comment`) torj ON tor.id = torj.id
            where torj.id IS NULL');
    }
}
