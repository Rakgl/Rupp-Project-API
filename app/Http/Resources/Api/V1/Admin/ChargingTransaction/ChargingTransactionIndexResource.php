<?php

namespace App\Http\Resources\Api\V1\Admin\ChargingTransaction;

use App\Helpers\Helper;
use App\Models\IdTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Maatwebsite\Excel\Concerns\ToArray;

class ChargingTransactionIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'transaction_number'   => $this->transaction_number,
            'date'                 => Helper::formatDate($this->created_at->setTimezone('Asia/Phnom_Penh')),
            'final_cost'           => $this->final_cost,
            'payment_method'       => $this->payment_method,
            'duration'             => Helper::formatDuration($this->duration_second),
            'energy'               => number_format($this->energy_consumed / 1000, 2),
            'status'               => Helper::formatStatus($this->status),

            // Station
            'station_name'         => $this->station_name ?? 'N/A',

            // Customer
            'customer_name'        => $this->customer_name ?? 'N/A',
            'customer_phone'       => $this->customer_phone ?? 'N/A',

            // Charge point
            'cp_serial_number'     => $this->cp_serial_number,
            'cp_type'              => $this->cp_type,
            'cp_max_charging_power'=> $this->cp_max_charging_power,
            'cp_ocpp_status'       => Helper::formatStatus($this->cp_ocpp_status),

            // Connector
            'connector_number'     => $this->connector_number,
            'connector_name'       => $this->connector_name,
            'connector_ocpp_status'=> Helper::formatStatus($this->connector_ocpp_status),

            // SOC
            'soc'                  => $this->soc ?? 'N/A',
        ];
    }

}
