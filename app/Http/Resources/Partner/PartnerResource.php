<?php

namespace App\Http\Resources\Partner;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
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
            'status'=>'pending',
            'by'=>'publisher',
            'href' => [
//                'subject' => route('subject.show', $this->id)
            ],
        ];
    }
}
