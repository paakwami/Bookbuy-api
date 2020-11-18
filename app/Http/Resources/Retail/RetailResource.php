<?php

namespace App\Http\Resources\Retail;

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
            'name' => $this->name,
            'phone'=>$this->phone,
            'location'=>$this->location,
            'href' => [
//                'subject' => route('subject.show', $this->id)
            ],
        ];
    }
}
