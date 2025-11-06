<?php

namespace Mortezaa97\Factors\Http\Controllers;

use Mortezaa97\Factors\Models\FactorHasItem;
use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Mortezaa97\Factors\Http\Resources\FactorHasItemResource;

class FactorHasItemController
{
    public function index()
    {
        Gate::authorize('viewAny', FactorHasItem::class);
        return FactorHasItemResource::collection(FactorHasItem::all());
    }

    public function store(Request $request)
    {
        Gate::authorize('create', FactorHasItem::class);
        try {
            DB::beginTransaction();
            // TODO: Implement store logic
            $factorHasItem = FactorHasItem::create($request->validated());
            DB::commit();
            return new FactorHasItemResource($factorHasItem);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($exception->getMessage(),419);
        }
    }

    public function show(FactorHasItem $factorHasItem)
    {
        Gate::authorize('view', $factorHasItem);
        return new FactorHasItemResource($factorHasItem);
    }

    public function update(Request $request, FactorHasItem $factorHasItem)
    {
        Gate::authorize('update', $factorHasItem);
        try {
            DB::beginTransaction();
            // TODO: Implement update logic
            $factorHasItem->update($request->validated());
            DB::commit();
            return new FactorHasItemResource($factorHasItem);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($exception->getMessage(),419);
        }
    }

    public function destroy(FactorHasItem $factorHasItem)
    {
        Gate::authorize('delete', $factorHasItem);
        try {
            DB::beginTransaction();
            $factorHasItem->delete();
            DB::commit();
            return response()->json("با موفقیت حذف شد");
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($exception->getMessage(),419);
        }
    }
}

