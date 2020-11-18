<?php

namespace App\Http\Resources\ItemStock;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemStockAgentCollection extends JsonResource
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
            'item_id'=>$this->item->id,
            'name' => $this->item->name,
            'edition'=>$this->item->edition,
            'price'=>$this->item->price,
            'class'=>$this->item->learner_stage->name,
            'subject'=>$this->item->series->subject->name,
            'remaining_stock'=>$this->remaining_stock,
            'href' => [
//                'subject' => route('subject.show', $this->id)
            ],

        ];
    }
}
