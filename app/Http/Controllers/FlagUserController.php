<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Repositories\User\User;
use App\Repositories\Client\Client;
use App\Repositories\Flag\FlagInterface;
use App\Repositories\User\UserInterface;
use App\Repositories\FlagUser\FlagUserInterface;
use App\Repositories\FlagUser\FlagUserCreateRequest;
use App\Repositories\ClientEmployeeLog\ClientEmployeeLog;

class FlagUserController extends Controller
{
    /**
     * @var $flagUserRepository - EloquentRepositoryFlagUser
     */
    private $flagUserRepository;

    /**
     * @var $userRepository - EloquentRepositoryUser
     */
    private $userRepository;

    /**
     * @var $flagRepository - EloquentRepositoryFlag
     */
    private $flagRepository;

    /**
     * FlagUserController constructor.
     *
     * @param FlagUserInterface $flagUserRepository
     * @param UserInterface $userRepository
     * @param FlagInterface $flagRepository
     */
    public function __construct(FlagUserInterface $flagUserRepository, UserInterface $userRepository, FlagInterface $flagRepository)
    {
        parent::__construct();

        $this->flagUserRepository = $flagUserRepository;
        $this->userRepository = $userRepository;
        $this->flagRepository = $flagRepository;
    }

    /**
     * UserController constructor.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(User $user)
    {
        // Get the available clients for this user
        $clients = $user->getAccessibleClientsQuery()->get();

        return view('flag-user.create')->with([
            'user'      => $user,
            'clients'   => $clients,
        ]);
    }

    /**
     * @param FlagUserCreateRequest $request
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(FlagUserCreateRequest $request, int $userId)
    {
        // Get the user for whom we are adding the Flag
        $user = $this->userRepository->make()->find($userId);

        // Get the Flag entity
        $newFlag = $this->flagRepository->make()->find($request->input('flag_id'));

        if (is_null($newFlag->days)) {
            $newExpirationDate = null;
            $newFlagStatus = 1;
        } else {
            $newExpirationDate = Carbon::now()->addDays($newFlag->days)->toDateTimeString();
            $newFlagStatus = 0;
        }

        if ($user->hasFlags()) {
            $flag = $user->lastFlag();
            if( (is_null($newFlag->days)) || (!is_null($newFlag->days) && !is_null($flag->pivot->expirationDate) && strtotime($newExpirationDate) > strtotime($flag->pivot->expirationDate)) ) {
                $flag->pivot->active = 0;
                $flag->pivot->save();
                $newFlagStatus = 1;
            }
        } else {
            $newFlagStatus = 1;
        }

        // Assign flag data
        $flagExtraOptions = [
            'comment'           => $request->input('comment'),
            'active'            => $newFlagStatus,
            'expirationDate'    => $newExpirationDate,
        ];

        if ($newFlag->client_specific) {
            $flagExtraOptions['client_id'] = $request->input('client');
        }

        $user->flags()->attach($request->input('flag_id'), $flagExtraOptions);

        // Remove user from client(s) (if needed)
        if($newFlag->client_removal) {
            // Check if we need to remove one single client OR all user's clients
            if($newFlag->client_specific) {
                $client = Client::where('id', $request->input('client'))->first();
                if($user->id == $client->employee->id) {
                    // Remove $user for a specific client
                    $this->removeEmployee($client);
                }
                else {
                    $this->removeAllTasks($user, $client);
                }
            }
            else {
                // Remove $user for each client that is assigned to
                $clients = Client::where('employee_id', $user->id)->get();
                foreach($clients as $client) {
                    $this->removeEmployee($client);
                }
                $this->removeAllTasks($user);

            }
        }

        // Check if we need to send an SMS
        $message = trim($newFlag->sms);
        if(!is_null($newFlag->sms) && !empty($message) ) {
            // Add the client name to the message if char lenght is ok.
            if ($newFlag->client_specific) {
                $client = Client::where('id', $request->input('client'))->first();

                $client_name_variable = '[[clientname]]';
                //calculate how long name can fit
                $client_name_possible_length = 160 - (strlen($message) - strlen($client_name_variable)) + 1;
                $cut_client_name = $client_name_possible_length > 0 ? trim(substr($client->name, 0, $client_name_possible_length)) : '';
                //replace variable with cut client name
                $message = str_replace($client_name_variable, $cut_client_name, $message);
            }

            $result = notification('sms')
                ->message($message)
                ->to($user->phone)
                ->saveSimpleSmsForApproving();
        }

        return redirect()
            ->action('UserController@show', $user)
            ->with('success', "User `{$user->name}` was flagged successfully.");
    }

    /**
     * @param \App\Repositories\Flag\Flag $flag
     * @param \App\Repositories\User\User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $flagId, int $userId)
    {
        $user = $this->userRepository->make()->find($userId);
        $flag = $this->userRepository->resetFlag($user);

        return redirect()
            ->action('UserController@show', $user)
            ->with('success', "Flag `{$flag->reason}` was removed.");
    }

    // Remove all tasks from the user
    public function removeAllTasks(User $user, Client $client = null)
    {
        // Unassign all the active tasks assigned to the user
        $user->tasks(false)->uncompleted()->where(function($query) use ($client) {
            if(!is_null($client)) return $query->where('client_id', $client->id);
        })
        ->update([
            'user_id' => null,
        ]);
    }


    // Remove all clients from employee
    public function removeEmployee(Client $client)
    {
        // Save current employee for later use
        $oldEmployeeId = $client->employee_id;

        // Check if log does not have entry from before and add it
        if (! $log = $client->employeeLogs()->where('user_id', $client->employee_id)->first()) {
            $log = $client->employeeLogs()->save(new ClientEmployeeLog([
                'user_id'     => $client->employee_id,
                'assigned_at' => $client->created_at,
            ]));
        }

        // Update rating (negative) for old user
        $log->update([
            'rating' => 0,
            'removed_at' => Carbon::now()
        ]);

        // Create new log for current assigned user
        $client->employeeLogs()->save(new ClientEmployeeLog([
            'user_id' => null,
            'assigned_at' => Carbon::now()
        ]));

        $clientDataToBeUpdated = [
            'employee_id' => null,
        ];
        if($client->manager_id == $client->employee_id) {
            $clientDataToBeUpdated['manager_id'] = null;
        }

        // Change employee on client level
        $client->update($clientDataToBeUpdated);

        // Make all tasks unassigned
        $client->tasks(false)
            ->uncompleted()
            ->where('user_id', $oldEmployeeId)
            ->update(['user_id' => null]);

        return true;
    }
}
