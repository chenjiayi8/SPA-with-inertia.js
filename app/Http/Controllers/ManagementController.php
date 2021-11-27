<?php

namespace App\Http\Controllers;

use App\Models\Management;
use Inertia\Inertia;
use Redirect;

class ManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Auth/Management', [
            'workspace' => Management::first(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreManagementRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
//        $attributes = request()->all();
//        $workspace = $attributes['workspace'];
//        $document = new Management($workspace);
//        ddd('store');
        $attributes = request()->all()['workspace'];
//        ddd($management);
        $management = Management::first();
        $management->update($attributes);
        return back()->withInput()->with('success', 'Workspace updated');
//        return Redirect::route('management');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Management $management
     * @return \Illuminate\Http\Response
     */
    public function show(Management $management)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Management $management
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $management = Management::first();
        return Inertia::render('Auth/Management', [
            'workspace' => $management,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateManagementRequest $request
     * @param \App\Models\Management $management
     * @return \Illuminate\Http\Response
     */
    public function update(Management $management)
    {

//        $attributes = request()->all()['workspace'];
////        ddd($management);
//        $management = Management::first();
//        $management->update($attributes);
//        return back()->with('success', 'Workspace updated');
//        ddd($management);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Management $management
     * @return \Illuminate\Http\Response
     */
    public function destroy(Management $management)
    {
        //
    }
}
