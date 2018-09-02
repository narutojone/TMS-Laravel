<?php

namespace App\Http\Controllers;

use App\Repositories\System\System;
use App\Repositories\System\SystemCreateRequest;
use App\Repositories\System\SystemInterface;
use App\Repositories\System\SystemUpdateRequest;

class SystemController extends Controller
{
    /**
     * @var $systemRepository - EloquentRepositorySystem
     */
    private $systemRepository;

    /**
     * SystemController constructor.
     *
     * @param SystemInterface $systemRepository
     */
    public function __construct(SystemInterface $systemRepository)
    {
        parent::__construct();

        $this->systemRepository = $systemRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Authorize request
        $this->authorize('index', System::class);

        $systems = $this->systemRepository->make()->paginate(30);

        return view('settings.systems.index')->with([
            'systems' => $systems,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        // Authorize request
        $this->authorize('create', System::class);

        return view('settings.systems.create');
    }

    /**
     * @param SystemCreateRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SystemCreateRequest $request)
    {
        // Authorize request
        $this->authorize('store', System::class);

        $system = $this->systemRepository->create($request->all());

        if ($request->input('default') == System::IS_DEFAULT) {
            $this->systemRepository->make()->where('id', '!=', $system->id)->update(['default'=> System::IS_NOT_DEFAULT]);
        }

        return redirect()
            ->route('systems.index')
            ->with('success', "New \"{$system->name}\" system has been created successfully.");
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(int $id)
    {
        // Authorize request
        $this->authorize('show', System::class);

        $system = $this->systemRepository->make()->find($id);

        return view('settings.systems.show')->with([
            'system' => $system,
        ]);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(int $id)
    {
        // Authorize request
        $this->authorize('edit', System::class);

        $system = $this->systemRepository->make()->find($id);

        return view('settings.systems.edit')->with([
            'system' => $system,
        ]);
    }

    /**
     * @param SystemUpdateRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SystemUpdateRequest $request, int $id)
    {
        // Authorize request
        $this->authorize('update', System::class);

        $system = $this->systemRepository->update($id, $request->all());


        if ($request->input('default') == System::IS_DEFAULT) {
            $this->systemRepository->make()->where('id', '!=', $system->id)->update(['default'=> System::IS_NOT_DEFAULT]);
        }

        return redirect()
            ->route('systems.show', $system)
            ->with('success', "System \"{$system->name}\" has been updated successfully.");
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        // Authorize request
        $this->authorize('destroy', System::class);

        $system = $this->systemRepository->make()->find($id);

        if ($system->clients()->count()) {
            return back()->with('info', "Can't delete system. Clients are assigned.");
        }

        if ($system->users()->count()) {
            return back()->with('info', "Can't delete system. Users are assigned.");
        }

        if ($system->default == System::IS_DEFAULT) {
            return back()->with('info', "Can't delete system. The system is marked as default.");
        }

        $this->systemRepository->delete($id);

        return redirect()
            ->route('systems.index')
            ->with('success', "System {$system->name} has been deleted successfully.");
    }
}
