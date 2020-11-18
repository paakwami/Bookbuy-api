<?php


namespace App\Http\Resources\Partner;


use Illuminate\Http\Resources\Json\JsonResource;

class PartnerCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone'=>$this->phone,
            'status'=>$this->pivot->status,
            'location'=>$this->location,
            'by'=>$this->pivot->by,
            'href' => [
//                'subject' => route('subject.show', $this->id)
            ],
        ];
    }
}

