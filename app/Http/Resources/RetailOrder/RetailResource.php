<?php

namespace App\Http\Resources\RetailOrder;

use App\Http\Resources\PublisherServe\PublisherServeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RetailResource extends JsonResource
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
            'sale' => $this->sale,
            'phone'=>$this->phone,
            'publisher'=>[
                PublisherServeResource::Collection($this->PublisherServe)
            ]

        ];
    }
}
