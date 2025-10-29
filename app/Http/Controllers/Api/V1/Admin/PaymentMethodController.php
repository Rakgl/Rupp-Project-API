<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\PaymentMethod\PaymentMethodIndexResource;
use App\Http\Requests\Api\V1\Admin\Mobile\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Helpers\AppHelper;
use App\Helpers\Helper;
use App\Http\Resources\Api\V1\Admin\AuditResource;
use App\Http\Resources\Api\V1\Admin\PaymentMethod\PaymentMethodActiveResource;
use App\Http\Resources\Api\V1\Admin\PaymentMethod\PaymentMethodEditResource;
use App\Http\Resources\Api\V1\Admin\PaymentMethod\PaymentMethodShowResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PaymentMethodController extends Controller
{
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
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        $data->getCollection()->transform(function ($item) {
            if ($item->image) {
                $item->image = asset('storage/' . $item->image);
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment Method retrieved successfully.',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentMethodRequest $request)
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/images', 'public');
        }

        PaymentMethod::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'status' => 'ACTIVE',
            'image' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment Method created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource for editing.
     */
    public function edit(string $id)
    {
        $data = PaymentMethod::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Payment Method not found'], 404);
        }
        return new PaymentMethodEditResource($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = PaymentMethod::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Payment Method not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new PaymentMethodShowResource($data)
        ]);
    }

    /**
     * Update the specified resource in storage.
     * IMPORTANT: The frontend must send this as a POST request with a `_method` field set to 'PUT'.
     */
    public function update(PaymentMethodRequest $request, string $id)
    {
        $data = PaymentMethod::find($id);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        }

        $imagePath = $data->image;
        if ($request->hasFile('image')) {
            if ($data->image) {
                Storage::disk('public')->delete($data->image);
            }
            $imagePath = $request->file('image')->store('uploads/images', 'public');
        }

        $data->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'status' => $request->status,
            'image' => $imagePath,
            'update_num' => $data->update_num + 1,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Record has been updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = PaymentMethod::find($id);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Payment Method not found'], 404);
        }

        // Use the Eloquent delete method for soft deleting.
        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment Method has been deleted.',
        ], 200);
    }

    public function audit($id)
    {
        // Use withTrashed() to find the record even if it's soft-deleted.
        $paymentMethod = PaymentMethod::withTrashed()->find($id);

        if (!$paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => "Payment Method not found."
            ], 404);
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