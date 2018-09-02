<?php

namespace App\Console\Commands;

use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use App\Repositories\ProcessedNotificationLog\ProcessedNotificationLogInterface;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class CleanDeclinedNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:clean-declined-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean notifications that are in a state of declined';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $processedNotificationLogRepository = app()->make(ProcessedNotificationLogInterface::class);
        $processedNotificationRepository = app()->make(ProcessedNotificationInterface::class);

        $processedNotificationsDeclined = $processedNotificationRepository->model()->where('status', '=', ProcessedNotification::STATUS_DECLINED)->get();

        $processedNotificationsSuccess = 0;
        $processedNotificationsError = 0;

        foreach ($processedNotificationsDeclined as $processedNotificationDeclined) {
            try {
                $data = $processedNotificationDeclined->toArray();
                $originalData = $data;
                unset($data['id']);

                // Add notification in the processed_notification_logs table (for logs)
                $processedNotificationLogRepository->create($data);

                // Delete the processed notification record so that we avoid growing the table (processed_notifications) to big
                $processedNotificationDeclined->delete();
                $processedNotificationsSuccess++;

                if ($processedNotificationDeclined->task) {
                    echo "\n \e[32m The template notification with id: ".$processedNotificationDeclined->id." task id: ".$processedNotificationDeclined->task->id." deleted succesfully. \e[0m \n";
                } else {
                    echo "\n \e[32m The template notification with id: ".$processedNotificationDeclined->id." task id: ".$data['task_id']."(task does not exists anymore),  deleted successfully. \e[0m \n";
                }

            } catch (ValidationException $e) {
                $processedNotificationsError++;
                echo "\n \e[31m Processed declined notification with id: " . $processedNotificationDeclined->id . " failed with the following errors: " . $e->getMessage() . " \e[0m \n";
                foreach ($e->errors() as $field => $errors) {
                    foreach ($errors as $error) {
                        echo "      \e[31m ".$field .": " . $error . " \e[0m \n";
                    }
                }
                print_r("      \e[31m The data given was: \e[0m \n");
                echo "      ";
                print_r($originalData);
            } catch (\Exception $e) {
                $processedNotificationsError++;
                echo "\n \e[31m Processed declined notification with id: " . $processedNotificationDeclined->id . " failed with error: " . $e->getMessage() . " \e[0m \n";
            }
        }

        echo "\n Successfully processed notifications: ".$processedNotificationsSuccess."\n";
        echo "\n Failed processed notifications: ".$processedNotificationsError."\n";
    }
}
