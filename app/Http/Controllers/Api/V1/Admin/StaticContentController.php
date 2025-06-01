<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Mobile\StaticContentRequest;
use App\Http\Resources\Api\V1\Admin\AuditResource;
use App\Models\StaticContent;
use Illuminate\Http\Request;
use App\Helpers\AppHelper;
use App\Helpers\Helper;
use App\Http\Resources\Api\V1\Admin\StaticContent\StaticContentEditResource;
use App\Http\Resources\Api\V1\Admin\StaticContent\StaticContentIndexResource;
use App\Http\Resources\Api\V1\Admin\StaticContent\StaticContentShowResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StaticContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$data = StaticContent::where(function ($q) use ($request) {
			if($request->search) {
				$q->where('title', 'like', '%' . $request->search . '%');
			}

            if ($request->title) {
                $q->where('title', 'like', '%' . $request->title . '%');
            }

            if ($request->status) {
                $q->where('status', $request->status);
            }

			if ($request->type) {
				$q->where('type', $request->type);
			}
		})->notDelete()->orderBy('created_at', 'desc')
			->paginate($request->per_page);
		$resource = StaticContentIndexResource::collection($data)->response()->getData(true);
		return response()->json([
			'data' => $resource['data'],
			'meta' => [
				'current_page' => $resource['meta']['current_page'],
				'last_page' => $resource['meta']['last_page'],
				'total' => $resource['meta']['total'],
			]
		]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StaticContentRequest $request)
    {
        $path = 'uploads/images';
        $imagePath = $request->hasFile('image') ? AppHelper::uploadImage($request->file('image'), $path) : null;
    
        Log::info('image path' . $imagePath);
		StaticContent::create([
			'title' => $request->title,
			'type' => $request->type,
			'content' => $request->content,
			'status' => $request->status,
			'image' => $imagePath,
			'created_at' => Carbon::now(),
			'created_by' => auth()->user()->id,
			'updated_at' => Carbon::now(),
		]);

        return response()->json([
            'success' => true,
            'message' => 'Static Content created successfully',
        ], 201);
    }

	public function edit(string $id) 
	{
		$data = StaticContent::find($id);

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Static Content not found'
            ],404);
        }
	return new StaticContentEditResource($data);
	}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = StaticContent::find($id);

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Static Content not found'
            ],404);
        }
        return new StaticContentShowResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StaticContentRequest $request, string $id)
    {
        $data = StaticContent::find($id);
    
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }
        $imagePath = Helper::updateImage($request, $data);

		$data->update([
			'title' => $request->title,
			'type' => $request->type,
			'content' => $request->content,
			'status' => $request->status,
			'image' => $imagePath,
			'update_num' => $data->update_num + 1,
			'updated_at' => Carbon::now(),
			'updated_by' => auth()->user()->id,
		]);
    
        return response()->json([
            'success' => true,
            'message' => 'Record has been updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = StaticContent::find($id);

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Static Content not found'
            ],404);
        }

        $data->status = 'DELETED';
        $data->save();

        return  response()->json([
            'success' => true,
            'message' => 'Static Content deleted successfully',
        ],200);
    }
    public function audit($id)
	{
		$staticContent = StaticContent::find($id);

		if (!$staticContent) {
			return response()->json([
				'success' => false,
				'message' => "Static Content not found."
			]);
		}
		$audits = $staticContent->audits()->with('user')->latest()->paginate(10);

		return response()->json([
			'success' => true,
			'items' => AuditResource::collection($audits)
		]);
	}
}
