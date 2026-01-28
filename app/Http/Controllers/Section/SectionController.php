<?php

namespace App\Http\Controllers\Section;

use App\Models\Section;
use App\Models\ClassLevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Fetch Data for the Table
        // Use eager loading (with('classLevel')) to prevent N+1 queries.
        $sections = Section::with('classLevel')->latest()->paginate(10);

        // 2. Fetch Data for the Modal Dropdown
        // We need the list of classes so the user can pick one when creating a section.
        $classLevels = ClassLevel::where('is_active', true)->get();

        return view('academics.sections.index', compact('sections', 'classLevels'));
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
    public function store(StoreSectionRequest $request)
    {
        $validatedData = $request->validated();

        Section::create($validatedData);


        // Return JSON success message
        return response()->json([
            'status' => 'success',
            'message' => 'Section created successfully!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, Section $section)
    {
        $validatedData = $request->validated();

        $section->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Section updated successfully!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
       $section->delete();

        return back()->with('success', 'Section deleted.');
    }
}
