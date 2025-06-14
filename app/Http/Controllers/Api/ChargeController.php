<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChargeRequest;
use App\Http\Resources\ChargeResource;
use App\Models\Charge;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $charges = Charge::query()->get();
        return CHargeResource::collection($charges);
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
    public function store(ChargeRequest $request)
    {
        $charge = Charge::query()->create($request->validated());
        $charge->subjects()->attach($request->subjects);
        return new ChargeResource($charge);
    }

    /**
     * Display the specified resource.
     */
    public function show(Charge $charge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Charge $charge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChargeRequest $request, Charge $charge)
    {
        Charge::query()->update($request->validated());
        $charge->subjects()->sync($request->subjects);
        return new ChargeResource($charge);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Charge $charge)
    {
        $charge->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ]);
    }
}
