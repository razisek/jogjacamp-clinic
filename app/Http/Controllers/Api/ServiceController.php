<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/service",
     *     summary="Create a new service",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Service created successfully"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function store(StoreServiceRequest $request)
    {
        try {
            $service = Service::create($request->validated());
            return response()->json([
                'message' => 'Service created successfully',
                'data' => $service
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
