<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentTypeRequest;
use App\Http\Resources\PaymentTypeResource;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payment_types = PaymentType::all();
        return PaymentTypeResource::collection($payment_types);
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
    public function store(PaymentTypeRequest $request)
    {
        $paymentType = PaymentType::query()->create($request->validated());
        return new PaymentTypeResource($paymentType);
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentType $paymentType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentType $paymentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentTypeRequest $request, PaymentType $paymentType)
    {
        $paymentType->update($request->validated());
        return new PaymentTypeResource($paymentType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentType $paymentType)
    {
        $paymentType->delete();
        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully'
        ]);
    }
}
