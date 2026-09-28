<?php

use App\Http\Controllers\Attachment\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('attachments', [AttachmentController::class, 'index'])->name('attachments.index');
    Route::get('attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::get('attachments/{attachment}/preview', [AttachmentController::class, 'preview'])->name('attachments.preview');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    Route::delete('attachments/{attachment}/force', [AttachmentController::class, 'forceDelete'])->withTrashed()->name('attachments.forceDelete');
});
