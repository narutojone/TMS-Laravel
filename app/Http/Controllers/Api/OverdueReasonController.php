<?php

namespace App\Http\Controllers\Api;

use App\Repositories\OverdueReason\OverdueReasonCreateRequest;
use App\Repositories\OverdueReason\OverdueReasonInterface;
use App\Repositories\OverdueReason\OverdueReasonMoveRequest;
use App\Repositories\OverdueReason\OverdueReasonTransformer;
use App\Http\Controllers\Controller;
use App\Repositories\OverdueReason\OverdueReasonUpdateRequest;
use Illuminate\Http\Request;

//  ! ! ! ! NOT USED ! ! ! ! - api routes pointing in this controller are also disabled(commented)

class OverdueReasonController extends Controller
{
     /**
     * @var $userRepository - EloquentRepositoryUser
     */
    private $overdueReasonRepository;

    /**
     * Instantiate a new controller instance.
     *
     * @param OverdueReasonInterface $overdueReasonRepository
     */
    public function __construct(OverdueReasonInterface $overdueReasonRepository)
    {
        parent::__construct();

        $this->overdueReasonRepository = $overdueReasonRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Repositories\OverdueReason\OverdueReasonCreateRequest  $request
     * @return array
     */
    public function store(OverdueReasonCreateRequest $request)
    {
        $reason = $this->overdueReasonRepository->create($request->all());

        return (new OverdueReasonTransformer)->transform($reason);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Repositories\OverdueReason\OverdueReasonUpdateRequest  $request
     * @param  int $id - id of the resource that is going to be updated
     * @return array
     */
    public function update(OverdueReasonUpdateRequest $request, int $id)
    {
        $reason = $this->overdueReasonRepository->update($id, $request->all());

        return (new OverdueReasonTransformer)->transform($reason);
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the resource that is going to be deleted (deleted means updated with the appropiate value for column active)
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, int $id)
    {
        $reason = $this->overdueReasonRepository->make()->find($id);
        $input = $reason->toArray();
        
        if ($request->input('active') == 1){
            $input['active'] = false;
            $message = 'Reason deactivated.';
        } else {
            $input['active'] = true;
            $message = 'Reason activated.';
        }

        $reason = $this->overdueReasonRepository->update($id, $input);

        return (new OverdueReasonTransformer)->transform($reason);
    }

    /**
     * Move the reason up or down.
     *
     * @param  \App\Repositories\OverdueReason\OverdueReasonMoveRequest  $request
     * @param  int $id - id of the resource
     * @return array
     */
    public function move(OverdueReasonMoveRequest $request, int $id)
    {
        $direction = $request->input('direction');
        $reason = $this->overdueReasonRepository->move($id, $direction);

        return (new OverdueReasonTransformer)->transform($reason);
    }
}