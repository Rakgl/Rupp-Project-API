<?php

namespace App\Http\Controllers\Api\V1\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TranslationController extends Controller
{
    /**
     * Display a paginated list of translations for the admin panel.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

        // Fetch paginated translations from the database
        $translationsPaginator = Translation::query()
            ->where(function ($query) use ($request) {
                // General search across key and value columns
                if ($request->filled('search')) {
                    $searchTerm = '%' . $request->search . '%';
                    $query->where('key', 'LIKE', $searchTerm)
                        ->orWhere('value', 'LIKE', $searchTerm);
                }

                // Specific search for the 'key' column
                if ($request->filled('key')) {
                    $query->where('key', 'LIKE', '%' . $request->key . '%');
                }

                if ($request->filled('platform')) {
                    if($request->platform != 'ALL') {
                        $query->where('platform', $request->platform);
                    }
                }
            })
            ->where('status', '!=', 'DELETED')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        // Manually structure the response to match the format the Vue component expects,
        // which was previously created by the API Resource Collection.
        return response()->json([
            'success' => true,
            'message' => 'Translations retrieved successfully.',
            'data' => [
                'data' => $translationsPaginator->items(),
                'links' => [
                    'first' => $translationsPaginator->url(1),
                    'last' => $translationsPaginator->url($translationsPaginator->lastPage()),
                    'prev' => $translationsPaginator->previousPageUrl(),
                    'next' => $translationsPaginator->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $translationsPaginator->currentPage(),
                    'from' => $translationsPaginator->firstItem(),
                    'last_page' => $translationsPaginator->lastPage(),
                    'path' => $translationsPaginator->path(),
                    'per_page' => $translationsPaginator->perPage(),
                    'to' => $translationsPaginator->lastItem(),
                    'total' => $translationsPaginator->total(),
                ],
            ]
        ]);
    }


    /**
     * Display the specified translation.
     *
     * @param Translation $translation
     * @return JsonResponse
     */
    public function show(Translation $translation): JsonResponse
    {
        // Manually decode the value before sending the response.
        $translation->value = json_decode($translation->value);

        return response()->json([
            'success' => true,
            'message' => 'Translation retrieved successfully.',
            'data' => $translation,
        ]);
    }

    /**
     * Get all active translations formatted for a specific locale.
     *
     * @param string $locale The language code (e.g., 'en', 'fr').
     * @return JsonResponse
     */
    public function getTranslationsByLocale(string $locale): JsonResponse
    {
        Log::info($locale);
        try {
            $translations = Translation::where('status', 'ACTIVE')->get(['key', 'value']);
            $formattedTranslations = [];

            foreach ($translations as $translation) {
                $valueDecoded = json_decode($translation->value, true);
                if (isset($valueDecoded[$locale])) {
                    $this->setNestedArrayValue($formattedTranslations, $translation->key, $valueDecoded[$locale]);
                }
            }

            return response()->json($formattedTranslations);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not fetch translations.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to set a value in a nested array using a dot-notation key.
     *
     * @param array &$array The array to modify.
     * @param string $key The dot-notation key (e.g., 'nav.headings.core_administration').
     * @param mixed $value The value to set.
     * @return void
     */
    private function setNestedArrayValue(array &$array, string $key, $value): void
    {
        $keys = explode('.', $key);
        $temp = &$array;

        foreach ($keys as $nestedKey) {
            if (!isset($temp[$nestedKey]) || !is_array($temp[$nestedKey])) {
                $temp[$nestedKey] = [];
            }
            $temp = &$temp[$nestedKey];
        }

        $temp = $value;
    }
}