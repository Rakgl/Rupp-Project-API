<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $services = Service::where('status', 'ACTIVE')->latest()->paginate(10);

        return response()->json($services);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        if ($service->status !== 'ACTIVE') {
            return response()->json(['message' => 'Service not found'], 404);
        }
        return response()->json($service);
    }
}
