<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExemptionReasonRequest;
use App\Http\Resources\ExemptionReasonResource;
use App\Models\ExemptionReason;
use Illuminate\Http\Request;

class ExemptionReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exemption_reasons = ExemptionReason::all();
        return ExemptionReasonResource::collection($exemption_reasons);
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
    public function store(ExemptionReasonRequest $request)
    {
        $exemption_reason = ExemptionReason::query()->create($request->validated());
        return new ExemptionReasonResource($exemption_reason);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExemptionReason $exemptionReason)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExemptionReason $exemptionReason)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExemptionReasonRequest $request, ExemptionReason $exemptionReason)
    {
        $exemptionReason->update($request->validated());
        return new ExemptionReasonResource($exemptionReason);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExemptionReason $exemptionReason)
    {
        $exemptionReason->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ]);
    }
}
