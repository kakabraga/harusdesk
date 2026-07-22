<?php

namespace App\Http\Controllers\Sector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Sector\StoreSectorRequest;
use App\Actions\Sector\CreateSectorAction;
use App\Actions\Sector\DeleteSectorAction;
use App\Actions\Sector\ListSectorAction;
use App\Helpers\ApiResponse;
use App\Http\Resources\Sector\CreateSectorResource;
use App\Http\Resources\Sector\SectorResource;
use App\Models\Sector;
class SectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListSectorAction $action)
    {
        return ApiResponse::success(SectorResource::collection($action->execute()), 'List');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreSectorRequest $request, CreateSectorAction $action)
    {
        return ApiResponse::success(new CreateSectorResource($action->execute($request->toDTO())));
    }

    /**
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        return new SectorResource($sector);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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
