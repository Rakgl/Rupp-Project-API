<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\News\StoreNewsRequest;
use App\Http\Requests\Api\V1\Admin\News\UpdateNewsRequest;
use App\Http\Resources\Api\V1\Admin\News\NewsResource;
use App\Helpers\AppHelper;
use App\Models\News;
use Exception;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return NewsResource::collection(News::latest()->paginate(10));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $imagePath = AppHelper::uploadImage($request->file('image'), 'uploads/news');
                
                $data['image_url'] = $imagePath;
                
                unset($data['image']);
            }

            $news = News::create($data);

            return new NewsResource($news);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create news.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        return new NewsResource($news);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsRequest $request, News $news)
    {
        try {
            $data = $request->validated();

            // Handle Image Update
            if ($request->hasFile('image')) {
                // Delete the old image
                if ($news->image_url) {
                    Storage::delete($news->image_url);
                }
                
                $imagePath = AppHelper::uploadImage($request->file('image'), 'uploads/news');
                $data['image_url'] = $imagePath;
            }

            $news->update($data);
            return new NewsResource($news);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update news.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        try {
            $news->delete();
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete news.', 'message' => $e->getMessage()], 500);
        }
    }
}
