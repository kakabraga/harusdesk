<?php

namespace App\Http\Controllers\Plan;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Actions\Plan\CreatePlanAction;
use App\Actions\Plan\DestroyPlanAction;
use App\Http\Resources\Plan\CreatePlanResource;
use App\Models\Plan;
class PlanController extends Controller
{
    public function store(StorePlanRequest $request, CreatePlanAction $action) {

        return ApiResponse::success( new CreatePlanResource($action->execute($request->toDTO())), 'Plan registered successfully.');

    }

    public function destroy(Plan $plan, DestroyPlanAction $action) {

        $action->execute($plan);

        return response()->noContent();
        
    }
}
