<?php

namespace App\Http\Resources\Series;

use App\Http\Resources\Classgroup\ClassgroupCollection;
use App\Http\Resources\Item\ItemCollection;
use App\Http\Resources\Item\ItemStoreResource;
use App\Http\Resources\Learner\LearnerStageCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class SeriesCollection extends JsonResource
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
            'subject_name'=> $this->subject->name,
            'subject_id'=> $this->subject->id,
            'classgroup'=>[
                'id'=> $this->classgroup->id,
                'name'=>$this->classgroup->Name,
                'Classes'=>[
                    LearnerStageCollection::collection($this->classgroup->LearnerStage)
                ]

            ],
            'items'=>[
                ItemStoreResource::collection($this->Item)
            ]
        ];
    }
}
