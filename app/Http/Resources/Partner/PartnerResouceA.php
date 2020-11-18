<?php

namespace App\Http\Resources\Partner;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResouceA extends JsonResource
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
            'status'=>'approved',
            'by'=>'publisher',
            'href' => [
//                'subject' => route('subject.show', $this->id)
            ],
        ];
    }
}
