<?php

namespace App\Http\Resources\AgentPayment;

use Illuminate\Http\Resources\Json\JsonResource;

class AgentPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount'=>$this->amount,
            'payment_method'=>$this->paymentmethod->name,
            'made_on'=>$this->created_at
        ];
    }
}
