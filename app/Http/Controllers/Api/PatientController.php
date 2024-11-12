<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/patient",
     *     summary="Create a new patient",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Patient created successfully"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function store(StorePatientRequest $request)
    {
        try {
            $patient = Patient::create($request->validated());
            return response()->json([
                'message' => 'Patient created successfully',
                'data' => $patient
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
