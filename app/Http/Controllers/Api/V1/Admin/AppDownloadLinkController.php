<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppDownloadLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class AppDownloadLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $links = AppDownloadLink::where('is_active', true)->orderBy('platform')->get();
            return response()->json([
                'success' => true,
                'message' => 'App download links retrieved successfully.',
                'data' => $links,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching app download links: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:1024',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            $link = AppDownloadLink::findOrFail($id);
            $link->url = $request->input('url');

            // Generate a new QR code whenever the URL is updated
            $qrCode = new QrCode($link->url);
            $writer = new SvgWriter();
            $svgResult = $writer->write($qrCode);
            $link->qr_code_svg = $svgResult->getString();
            
            $link->save();

            return response()->json([
                'success' => true,
                'message' => 'Download link updated successfully.',
                'data' => $link,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Link not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Error updating download link with ID {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during update.'], 500);
        }
    }
}
