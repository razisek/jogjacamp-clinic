<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCheckupQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function handle(): void
    {
        $diagnose = $this->appointment->diagnose;
        $services = Service::all();

        $requiredServices = match (strtolower($diagnose->name)) {
            'ringan' => $services->where('name', 'Obat'),
            'berat' => $services->whereIn('name', ['Obat', 'Rawat Inap']),
            'kritis' => $services->whereIn('name', ['Obat', 'Rawat Inap', 'ICU']),
            default => collect(),
        };

        foreach ($requiredServices as $service) {
            $this->appointment->checkupProgress()->create([
                'service_id' => $service->id,
                'status' => 0,
            ]);
        }
    }
}
