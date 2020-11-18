<?php

namespace App\Http\Resources\Item;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemCollection extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'edition' => $this->edition,
            'subject_name' => $this->series->subject->name,
            'publishing_house' => $this->series->user->name,
            'series_name'=> $this->series->name,
            'class' => $this->learner_stage->name,
            'picture'=>$request->root().$this->book_image,
            'remaining_stock'=>$this->itemstockpublisher->remaining_stock,
            'href' => [
                'item' => route('item.show', $this->id)
            ],
        ];
    }
}
