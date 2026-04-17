<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $paid = $this->payments_sum_amount ?? 0;
        $balance = $this->total_amount - $paid;

        return [
            'invoice_number' => $this->invoice_number,
            'total' => $this->total_amount,
            'paid' => $paid,
            'balance' => $balance,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'items' => InvoiceItemResource::collection(
                $this->whenLoaded('items')
            ),
        ];
    }
}
