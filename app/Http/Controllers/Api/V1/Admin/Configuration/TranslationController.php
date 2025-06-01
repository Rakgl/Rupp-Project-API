<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\Admin\TranslationRequest;
use App\Models\Translation;
use App\Http\Resources\Api\V1\Admin\TranslationResource;
use App\Http\Resources\Api\V1\Admin\AdminTranslationResource;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $translations = Translation::where(function ($q) use ($request) {
            if($request->search) {
                $q->where('key', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('value', 'LIKE', '%' . $request->search . '%');
            }

            if ($request->key) {
                $q->where('key', 'LIKE', '%' . $request->key . '%');
            }

            if ($request->value) {
                $q->where('value', 'LIKE', '%' . $request->value . '%');
            }

            if ($request->platform) {
                $q->where('platform', $request->platform);
            }

        })->notDelete()->orderBy('created_at', 'desc')->paginate($request->per_page);

        $translations = TranslationResource::collection($translations)->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $translations['data'],
            'meta' => [
                'current_page' => $translations['meta']['current_page'],
                'last_page' => $translations['meta']['last_page'],
                'total' => $translations['meta']['total'],
            ]
        ]);
    }

    public function store(TranslationRequest $request)
    {
        $status = Array();

        Translation::create([
            'key' => $request->key,
            'value' => $request->value,
            'platform' => $request->platform,
            'status' => $request->status,
        ]);

        $status['success'] = true;
        $status['message'] = 'Translation created successfully';
        return response()->json($status);
    }

    // public function show($id)
    // {
    //     $status = array();

    //     $translation = Translation::find($id);

    //     if(!$translation) {
    //         $status["success"] = false;
    //         $status["message"] = "Translation not found.";
    //         return response()->json($status);
    //     }

    //     $status["success"] = true;
    //     $status["message"] = "Shop found.";
    //     $status["data"] = new TranslationResource($translation);
    //     return response()->json($status);
    // }

    // public function update(TranslationRequest $request, $id)
    // {
    //     $status = array();

    //     $translation = Translation::find($id);

    //     if(!$translation) {
    //         $status["success"] = false;
    //         $status["message"] = "Translation not found.";
    //         return response()->json($status);
    //     }

    //     $translation->update([
    //         'key' => $request->key,
    //         'value' => $request->value,
    //         'platform' => $request->platform,
    //         'status' => $request->status,
    //     ]);

    //     $status['success'] = true;
    //     $status['message'] = 'Translation updated successfully';
    //     $status['data'] = new TranslationResource($translation);
    //     return response()->json($status);
    // }

    // public function destroy($id)
    // {
    //     $status = array();

    //     $translation = Translation::find($id);

    //     if(!$translation) {
    //         $status["success"] = false;
    //         $status["message"] = "Translation not found.";
    //         return response()->json($status);
    //     }

    //     $translation->update(
    //         [
    //             'status' => 3
    //         ]
    //     );

    //     $status['success'] = true;
    //     $status['message'] = 'Translation deleted successfully';
    //     return response()->json($status);
    // }

    // public function getTranslations(Request $request){
    //     $translations = Translation::admin()->active()->notDelete()->get();
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Success.',
    //         'data' => AdminTranslationResource::collection($translations),
    //     ]);
    // }
}
