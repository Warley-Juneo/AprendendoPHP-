<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lead = Lead::all();

        return response()->json($lead, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|numeric|digits:11',
        ]);

        $request['user_id'] = $request->user()->id;

        $lead = Lead::create($request->all());

        return response()->json($lead, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $lead = Lead::findOrFail($id);
            return response()->json($lead, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|numeric|digits:11',
        ]);

        try {
            $lead = Lead::findOrFail($id);
            $lead->update($request->all());
            return response()->json($lead, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $lead = Lead::findOrFail($id);
            $lead->delete();

            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
    }
}
