<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Flag\FlagCreateRequest;
use App\Repositories\Flag\FlagInterface;
use App\Repositories\Flag\FlagTransformer;
use App\Repositories\Flag\FlagUpdateRequest;
use Illuminate\Http\Request;

class FlagsController extends Controller
{
    /**
     * @var $flagRepository - EloquentRepositoryFlag
     */
    private $flagRepository;

    /**
     * FlagsController constructor.
     *
     * @param FlagInterface $flagRepository
     */
    public function __construct(FlagInterface $flagRepository)
    {
        $this->middleware('admin_only');
        parent::__construct();

        $this->flagRepository = $flagRepository;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $flags = $this->flagRepository->make()->paginate(25);

        return $flags;
    }

    /**
     * @param FlagCreateRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FlagCreateRequest $request)
    {
        $flag = $this->flagRepository->create($request->all());

        return (new FlagTransformer)->transform($flag);
    }

    /**
     * @param FlagUpdateRequest $request
     * @param int $id
     * @return Flag|\Illuminate\Http\RedirectResponse|void
     */
    public function update(FlagUpdateRequest $request, int $id)
    {
        $flag = $this->flagRepository->update($id, $request->all());

        return (new FlagTransformer)->transform($flag);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $this->flagRepository->delete($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'ok']);
        }
    }
}