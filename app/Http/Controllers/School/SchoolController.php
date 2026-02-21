<?php

namespace App\Http\Controllers\School;

use App\Models\School;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schools = School::all();

        return view('school.index', compact('schools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('school.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSchoolRequest $request)
    {
        $validatedData = $request->validated();


        School::create($validatedData);

        return redirect()->route('school.index')->with('success', 'School created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        return view('school.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        return view('school.edit',compact('school'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
    $validatedData = $request->validated();

    // Option A: Update only specific fields from the validated array
    $school->update($validatedData);

    return redirect()->route('school.index')->with('success', 'School updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('school.index')->with('success', 'School deleted successfully');
    }
}
