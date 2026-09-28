<?php

namespace App\Http\Controllers\Attachment;

use App\Actions\Attachments\DeleteAttachmentAction;
use App\Actions\Attachments\ForceDeleteAttachmentAction;
use App\Actions\Attachments\ListAttachmentAction;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\ListAttachmentRequest;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    /**
     * List all attachments with filters (SuperAdmin only).
     */
    public function index(ListAttachmentRequest $request, ListAttachmentAction $action): JsonResponse
    {
        $this->authorize('viewAny', Attachment::class);

        return ApiResponse::success(AttachmentResource::collection($action->execute($request->toDTO(), $request->user())));
    }

    /**
     * Display the specified attachment metadata.
     */
    public function show(Attachment $attachment): JsonResponse
    {
        $this->authorize('view', $attachment);

        return ApiResponse::success(new AttachmentResource($attachment));
    }

    /**
     * Download the attachment file.
     */
    public function download(Attachment $attachment): StreamedResponse
    {
        $this->authorize('download', $attachment);

        $disk = config('filesystems.default', 'local');

        if (! Storage::disk($disk)->exists($attachment->path)) {
            abort(404, 'The requested file was not found on storage.');
        }

        return Storage::disk($disk)->download(
            $attachment->path,
            $attachment->original_name
        );
    }

    /**
     * Preview the attachment file inline in browser.
     */
    public function preview(Attachment $attachment): StreamedResponse
    {
        $this->authorize('download', $attachment);

        $disk = config('filesystems.default', 'local');

        if (! Storage::disk($disk)->exists($attachment->path)) {
            abort(404, 'The requested file was not found on storage.');
        }

        return Storage::disk($disk)->response(
            $attachment->path,
            $attachment->original_name
        );
    }

    /**
     * Remove the specified attachment from storage and database.
     */
    public function destroy(Attachment $attachment, DeleteAttachmentAction $action)
    {
        $this->authorize('delete', $attachment);

        $action->execute($attachment);

        return response()->noContent();
    }

    /**
     * Force delete the specified attachment from storage and database.
     */
    public function forceDelete(Attachment $attachment, ForceDeleteAttachmentAction $action)
    {
        $this->authorize('forceDelete', $attachment);

        $action->execute($attachment);

        return response()->noContent();
    }
}
