<?php

namespace App\Http\Resources\PublisherServe;

use Illuminate\Http\Resources\Json\JsonResource;

class PublisherServeResource extends JsonResource
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
            'id'=>$this->id,
            'disbursed'=>$this->disbursed,
            'retail_order_id'=>$this->retail_order_id,
            'sale'=>$this->sale,
            'served'=>$this->served,
            'made_on'=>$this->created_at,
            'retailOrder'=>[
               $this->retailOrder
            ]

        ];
    }
}
