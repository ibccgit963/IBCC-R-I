<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super-admin'); // Only Super Admin can manage centers
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $centers = Center::all();
        return view('centers.index', compact('centers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('centers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:255', Rule::unique('centers', 'name')->whereNull('deleted_at')],
            'code'                    => ['required', 'string', 'max:50',  Rule::unique('centers', 'code')->whereNull('deleted_at')],
            'default_courier_company' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['city'] = $validated['name'];

        Center::create($validated);

        return redirect()->route('centers.index')
                         ->with('success', 'Center created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Center $center)
    {
        return view('centers.show', compact('center'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Center $center)
    {
        return view('centers.edit', compact('center'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Center $center)
    {
        $validated = $request->validate([
            'name'                    => ['required', 'string', 'max:255', Rule::unique('centers', 'name')->ignore($center->id)->whereNull('deleted_at')],
            'code'                    => ['required', 'string', 'max:50',  Rule::unique('centers', 'code')->ignore($center->id)->whereNull('deleted_at')],
            'default_courier_company' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['city'] = $validated['name'];

        $center->update($validated);

        return redirect()->route('centers.index')
                         ->with('success', 'Center updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Center $center)
    {
        $center->delete();

        return redirect()->route('centers.index')
                         ->with('success', 'Center deleted successfully.');
    }
}
