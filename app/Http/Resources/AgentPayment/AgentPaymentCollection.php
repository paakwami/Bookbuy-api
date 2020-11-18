<?php

namespace App\Http\Resources\AgentPayment;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AgentPaymentCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
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
