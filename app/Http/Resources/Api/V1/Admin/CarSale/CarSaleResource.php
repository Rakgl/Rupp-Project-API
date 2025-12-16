<?php

namespace App\Http\Resources\Api\V1\Admin\CarSale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'car' => $this->whenLoaded('car', function () {
                return [
                    'id' => $this->car->id,
                    'model_id' => $this->car->model_id,
                    'model' => $this->car->relationLoaded('model') ? [
                        'id' => $this->car->model->id,
                        'name' => $this->car->model->name,
                        'brand' => $this->car->model->relationLoaded('brand') ? [
                            'id' => $this->car->model->brand->id,
                            'name' => $this->car->model->brand->name,
                            'image_url' => $this->car->model->brand->image_url,
                        ] : null,
                    ] : null,
                ];
            }),
            'buyer' => $this->whenLoaded('buyer', function () {
                return [
                    'id' => $this->buyer->id,
                    'name' => $this->buyer->name,
                    'email' => $this->buyer->email,
                ];
            }),
            'final_price' => $this->final_price,
            'status' => $this->status,
            'payments' => $this->whenLoaded('payments', fn() => $this->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'method' => $payment->method,
                    'status' => $payment->status,
                ];
            })),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
