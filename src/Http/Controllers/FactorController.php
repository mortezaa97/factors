<?php

namespace Mortezaa97\Factors\Http\Controllers;

use Mortezaa97\Factors\Models\Factor;
use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Mortezaa97\Factors\Http\Resources\FactorResource;

class FactorController
{
    public function index()
    {
        Gate::authorize('viewAny', Factor::class);
        return FactorResource::collection(Factor::all());
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Factor::class);
        try {
            DB::beginTransaction();
            // TODO: Implement store logic
            $factor = Factor::create($request->validated());
            DB::commit();
            return new FactorResource($factor);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($exception->getMessage(),419);
        }
    }

    public function show(Factor $factor)
    {
        Gate::authorize('view', $factor);
        return new FactorResource($factor);
    }

    public function update(Request $request, Factor $factor)
    {
        Gate::authorize('update', $factor);
        try {
            DB::beginTransaction();
            // TODO: Implement update logic
            $factor->update($request->validated());
            DB::commit();
            return new FactorResource($factor);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($exception->getMessage(),419);
        }
    }

    public function destroy(Factor $factor)
    {
        Gate::authorize('delete', $factor);
        try {
            DB::beginTransaction();
            $factor->delete();
            DB::commit();
            return response()->json("با موفقیت حذف شد");
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($exception->getMessage(),419);
        }
    }
}

