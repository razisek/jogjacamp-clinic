<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Diagnose\StoreDiagnoseRequest;
use App\Models\Diagnose;
use Illuminate\Http\Request;

class DiagnoseController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/diagnose",
     *     summary="Create a new diagnose",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Diagnose created successfully"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function store(StoreDiagnoseRequest $request)
    {
        try {
            $diagnose = Diagnose::create($request->validated());
            return response()->json([
                'message' => 'Diagnose created successfully',
                'data' => $diagnose
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
