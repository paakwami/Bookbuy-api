<?php

namespace App\Http\Resources\Item;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemStoreResource extends JsonResource
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
            'price' => $this->price,
            'sale' => $this->sale,
            'edition' => $this->edition,
            'subject_id' => $this->series->subject->id,
            'subject_name' => $this->series->subject->name,
            'publishing_house' => $this->series->user->name,
            'series_id'=> $this->series->id,
            'class_id' => $this->learner_stage->id,
            'class_name' => $this->learner_stage->name,
            'picture'=>$request->root().$this->book_image,
            'remaining_stock'=>$this->itemstockpublisher->remaining_stock,
            'href' => [
                'item' => route('item.show', $this->id)
            ],
        ];
    }
}
