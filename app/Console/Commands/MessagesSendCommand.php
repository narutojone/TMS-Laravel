<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MessagesSendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:send {queue?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all messages in emails, sms queues.';

    /**
     * MessagesSendCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $queue = $this->argument('queue');

        if ($queue) {
            $this->line('Starting work on queue ' . $queue);
            $this->line('--------------------------------------------');
            $this->call('queue:work-and-exit', ['--queue' => $queue, '--tries' => 1]);
        } else {
            $this->line('Starting work on queue emails and sms queues');
            $this->line('--------------------------------------------');
            $this->call('queue:work-and-exit', ['--queue' => 'emails,sms', '--tries' => 1]);
        }
    }
}
