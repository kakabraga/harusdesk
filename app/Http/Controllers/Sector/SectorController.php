<?php

namespace App\Http\Controllers\Sector;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sector\StoreSectorRequest;
use App\Http\Requests\Sector\ListSectorRequest;
use App\Http\Requests\Sector\UpdateSectorRequest;
use App\Actions\Sector\CreateSectorAction;
use App\Actions\Sector\DeleteSectorAction;
use App\Actions\Sector\ListSectorAction;
use App\Actions\Sector\UpdateSectorAction;
use App\Helpers\ApiResponse;
use App\Http\Resources\Sector\CreateSectorResource;
use App\Http\Resources\Sector\SectorResource;
use App\Models\Sector;
class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListSectorRequest $request, ListSectorAction $action)
    {
        $this->authorize('viewAny', Sector::class);
        return ApiResponse::success(SectorResource::collection($action->execute()), 'List');

    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreSectorRequest $request, CreateSectorAction $action)
    {
        $this->authorize('create', Sector::class);
        return ApiResponse::success(new CreateSectorResource($action->execute($request->toDTO())));
    }

    /**
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        $this->authorize('view', $sector);
        return ApiResponse::success(new SectorResource($sector));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectorRequest $request, Sector $sector, UpdateSectorAction $action)
    {
        $this->authorize('update', $sector);
        $dto = $request->toDTO();
        return ApiResponse::success(new SectorResource($action->execute($sector, $dto)));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector, DeleteSectorAction $action)
    {
        $this->authorize('delete', $sector);

        $action->execute($sector);

        return response()->noContent();
    }
}
