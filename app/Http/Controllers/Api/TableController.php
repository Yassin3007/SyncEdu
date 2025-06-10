<?php

namespace App\Http\Controllers\Api;

use App\Filters\LessonFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Http\Resources\TableResource;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = new LessonFilter(Request());
        $tables = Table::query()->with(['teacher','subject'])->filter($filter) ;
        if(Request()->boolean('paginate')){
            $perPage = Request()->input('per_page', 15);
            $tables = $tables->paginate($perPage);
        }else{
            $tables = $tables->get();
        }
        return TableResource::collection($tables);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonRequest $request)
    {
        $validated = $request->validated();
        $validated['day'] = Table::DAYS[$request->day];
        $table = Table::query()->create($validated);
        return new TableResource($table);

    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        return new TableResource($table);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(LessonRequest $request, Table $table)
    {
        $validated = $request->validated();
        $table->update($validated);
        return new TableResource($table);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        $table->delete();
        return apiResponse('api.success');
    }
}
