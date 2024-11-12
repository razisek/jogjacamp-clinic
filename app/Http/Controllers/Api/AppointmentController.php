<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Jobs\ProcessCheckupQueue;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/appointment",
     *     summary="Create a new appointment",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"patient_id", "diagnose_id"},
     *             @OA\Property(property="patient_id", type="integer"),
     *             @OA\Property(property="diagnose_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Appointment created successfully"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            $appointment = Appointment::create($request->validated());
            ProcessCheckupQueue::dispatch($appointment);
            return response()->json([
                'message' => 'Appointment created successfully',
                'data' => $appointment
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/appointment/{id}",
     *     summary="Get appointment details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Appointment details"),
     *     @OA\Response(response=404, description="Appointment not found")
     * )
     */
    public function show(Appointment $appointment)
    {
        try {
            return response()->json([
                'id' => $appointment->id,
                'patient' => $appointment->patient,
                'diagnose' => $appointment->diagnose,
                'checkup' => $appointment->checkupProgress->map(function ($progress) {
                    return [
                        'id' => $progress->id,
                        'service' => $progress->service,
                        'status' => $progress->status,
                    ];
                }),
                'status' => $appointment->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/appointment/{id}",
     *     summary="Update appointment status",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Appointment updated successfully"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        try {
            $appointment->update($request->validated());
            return response()->json([
                'message' => 'Appointment updated successfully',
                'data' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
