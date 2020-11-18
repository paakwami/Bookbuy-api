<?php

namespace App\Http\Resources\Learner;

use Illuminate\Http\Resources\Json\JsonResource;

class LearnerStageCollection extends JsonResource
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
//            'href' => [
//                'learnerstage' => route('learnerstage.show', $this->id)
//            ],
        ];

    }
}
