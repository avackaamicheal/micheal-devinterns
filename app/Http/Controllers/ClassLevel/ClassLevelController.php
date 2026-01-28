<?php

namespace App\Http\Controllers\ClassLevel;

use App\Models\ClassLevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassLevelRequest;
use App\Http\Requests\UpdateClassLevelRequest;

class ClassLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch classes scoped to the active school (handled by Model Trait)
        // withCount('sections') is great for showing stats like "Grade 1 (3 Sections)"
        $classLevels = ClassLevel::withCount('sections')
                        ->latest()
                        ->paginate(10);

        return view('academics.classlevels.index', compact('classLevels'));
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
    public function store(StoreClassLevelRequest $request)
    {
        $validatedData = $request->validated();

        ClassLevel::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Class Level created successfully!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassLevel $classLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassLevel $classLevel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassLevelRequest $request, ClassLevel $classLevel)
    {

        $valdatedData = $request->validated();

        $classLevel->update($valdatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Class Level updated successfully!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassLevel $classLevel)
    {
       // The database cascade will handle deleting related sections automatically
        $classLevel->delete();

        return back()->with('success', 'Class Level deleted successfully.');
    }
}
