<?php

namespace Modules\Instagram\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;

class InstagramController extends CoreController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('instagram::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('instagram::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('instagram::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('instagram::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
