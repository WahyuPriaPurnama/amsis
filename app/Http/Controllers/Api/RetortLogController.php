<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RetortLog;
use Illuminate\Http\Request;

class RetortLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'temperature' => 'required|numeric|between:-999.99,999.99',
            'retort_id' => 'required|string|max:255',
        ]);

        $log = RetortLog::create($validatedData);
        return response()->json([
            'success' => true,
            'message' => 'Retort log created successfully',
            'data' => $log
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(RetortLog $retortLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RetortLog $retortLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RetortLog $retortLog)
    {
        //
    }
}
