<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Faq\FAQIndexResource;
use App\Http\Requests\Api\V1\Admin\Mobile\FaqRequest;
use App\Http\Resources\Api\V1\Admin\AuditResource;
use App\Http\Resources\Api\V1\Admin\Faq\FAQEditResource;
use App\Http\Resources\Api\V1\Admin\Faq\FAQShowResource;
use App\Models\Faq;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Faq::where(function ($q) use ($request) {
            if ($request->search) {
				$q->where('question', 'like', '%' . $request->search . '%')
					->orWhere('answer', 'like', '%' . $request->search . '%');
            }

            if ($request->answer) {
                $q->where('answer', 'like', '%' . $request->answer . '%');
            }

            if ($request->status) {
                $q->where('status', $request->status);
            }
		})
		->notDelete()
		->orderBy('created_at', 'desc')
		->paginate($request->per_page);

		$resource = FAQIndexResource::collection($data)->response()->getData(true);
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
    public function store(FaqRequest $request)
    {
		Faq::create([ 
			'question' => $request->question,
			'answer' => $request->answer,
			// 'category' => $request->category,
			'status' => $request->status,
			'created_by' => auth()->user()->id,
			'created_at' => Carbon::now()
		]);

        return response()->json([
            'success' => true,
            'message' => 'Faq created successfully',
        ], 201);

    }

	public function edit(string $id)
    {
        $data = Faq::find($id);
        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Faq Model not found'
            ],404);
        }
        return new FAQEditResource($data);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Faq::find($id);
        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Faq Model not found'
            ],404);
        }
        return new FAQShowResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FaqRequest $request, string $id)
    {
        $data = Faq::find($id);
    
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }

		$data->update([ 
			'question' => $request->question,
			'answer' => $request->answer,
			'status' => $request->status,
			'updated_by' => auth()->user()->id,
			'updated_at' => Carbon::now(),
			'update_num' => $data->update_num + 1
		]);
    
        return response()->json([
            'success' => true,
            'message' => 'FAQ has been updated successfully',
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Faq::find($id);

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Faq not found'
            ],404);
        }

        $data->status = 'DELETED';
        $data->save();

        return  response()->json([
            'success' => true,
            'message' => 'Faq deleted successfully',
        ],200);
    }

    public function audit($id)
	{
		$faq = Faq::find($id);

		if (!$faq) {
			return response()->json([
				'success' => false,
				'message' => "Faq not found."
			]);
		}
		$audits = $faq->audits()->with('user')->latest()->paginate(10);

		return response()->json([
			'success' => true,
			'items' => AuditResource::collection($audits)
		]);
	}
}
