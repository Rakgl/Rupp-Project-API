<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\PaymentMethod\PaymentMethodIndexResource;
use App\Http\Requests\Api\V1\Admin\Mobile\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Helpers\AppHelper;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Api\V1\Admin\AuditResource;
use App\Http\Resources\Api\V1\Admin\PaymentMethod\PaymentMethodActiveResource;
use App\Http\Resources\Api\V1\Admin\PaymentMethod\PaymentMethodEditResource;
use App\Http\Resources\Api\V1\Admin\PaymentMethod\PaymentMethodShowResource;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$data = PaymentMethod::where(function ($q) use ($request) {
            if ($request->search) {
				$q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->search) . '%']);
            }

            if ($request->name) {
                $q->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->status) {
                $q->where('status', $request->status);
            }
		})
		->notDelete()
		->orderBy('created_at', 'desc')
		->paginate($request->per_page);

		$resource = PaymentMethodIndexResource::collection($data)->response()->getData(true);
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
    public function store(PaymentMethodRequest $request)
    {
        $path = 'uploads/images';
        $imagePath = $request->hasFile('image') ? AppHelper::uploadImage($request->file('image'), $path) : null;
    
        Log::info('image path' . $imagePath);

		PaymentMethod::create([
			'name' => $request->name,
			'description' => $request->description,
			'type' => $request->type,
			'status' => $request->status,
			'image' => $imagePath,
			'created_at' => Carbon::now(),
			'created_by' => auth()->user()->id,
			'updated_at' => Carbon::now(),
		]);

        return response()->json([
            'success' => true,
            'message' => 'Payment Method created successfully',
        ], 201);

    }

	public function edit(string $id)	
	{
		$data = PaymentMethod::find($id);
		if(!$data){
			return response()->json([
				'success' => false,
				'message' => 'Payment Method not found'
			],404);
		}
		return new PaymentMethodEditResource($data);
	}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = PaymentMethod::find($id);

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Payment Method not found'
            ],404);
        }
        return new PaymentMethodShowResource($data);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentMethodRequest $request, string $id)
    {
        $data = PaymentMethod::find($id);
    
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }

        $imagePath = Helper::updateImage($request, $data);
    
		$data->update([
			'name' => $request->name,
			'description' => $request->description,
			'type' => $request->type,
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
        $data = PaymentMethod::find($id);

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Payment Methods not found'
            ],404);
        }

        $data->status = 'DELETED';
        $data->save();

        return  response()->json([
            'success' => true,
            'message' => 'Payment Methods deleted successfully',
        ],200);

    }

    public function audit($id)
	{
		$paymentMethod = PaymentMethod::find($id);

		if (!$paymentMethod) {
			return response()->json([
				'success' => false,
				'message' => "Payment Methods not found."
			]);
		}
		$audits = $paymentMethod->audits()->with('user')->latest()->paginate(10);

		return response()->json([
			'success' => true,
			'items' => AuditResource::collection($audits)
		]);
	}

	public function active(Request $request) 
	{ 
		$data = PaymentMethod::where(function ($q) use ($request) {
            if ($request->search) {
                $q->where('name', 'LIKE', '%' . $request->search . '%');
            }
        })
        ->active()
		->orderBy('created_at', 'desc')
        ->paginate($request->per_page);

		return PaymentMethodActiveResource::collection($data)->response()->getData(true);
	}
}
