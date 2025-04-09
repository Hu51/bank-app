<?php

namespace App\Http\Controllers;

use App\Models\MappingProfile;
use Illuminate\Http\Request;

class MappingProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mappingProfiles = MappingProfile::all();
        return view('mapping-profiles.index', compact('mappingProfiles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('mapping-profiles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'skip_rows' => 'required|integer|min:0',
            'transaction_title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'counterparty' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'transaction_date' => 'required|string|max:255',
            'amount' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reference_id' => 'required|string|max:255',
            'card_number' => 'nullable|string|max:255',
        ]);

        MappingProfile::create($validated);

        return redirect()->route('mapping-profiles.index')
            ->with('success', 'Mapping profile created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MappingProfile $mappingProfile)
    {
        return view('mapping-profiles.show', compact('mappingProfile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MappingProfile $mappingProfile)
    {
        return view('mapping-profiles.edit', compact('mappingProfile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MappingProfile $mappingProfile)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'skip_rows' => 'required|integer|min:0',
            'transaction_title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'counterparty' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'transaction_date' => 'required|string|max:255',
            'amount' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'reference_id' => 'required|string|max:255',
            'card_number' => 'nullable|string|max:255',
        ]);

        $mappingProfile->update($validated);

        return redirect()->route('mapping-profiles.index')
            ->with('success', 'Mapping profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MappingProfile $mappingProfile)
    {
        $mappingProfile->delete();

        return redirect()->route('mapping-profiles.index')
            ->with('success', 'Mapping profile deleted successfully.');
    }
}
