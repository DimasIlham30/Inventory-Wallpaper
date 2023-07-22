<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('material.index');
    }


    public function create()
    {
        return view('material.create');
    }


    public function store(Request $request)
    {
        //
    }


    public function edit($id)
    {
        return view('material.edit');
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}