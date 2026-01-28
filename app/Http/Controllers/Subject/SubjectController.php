<?php

namespace App\Http\Controllers\Subject;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::latest()->paginate(10);

        return view('academics.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
        $validatedData = $request->validated();

       Subject::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Subject added to curriculum!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $validatedData = $request->validated();

        $subject->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Subject updated successfully!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return back()->with('success', 'Subject deleted successfully.');
    }
}
