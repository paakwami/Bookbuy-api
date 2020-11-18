<?php

namespace App\Http\Resources\Classgroup;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassgroupCollection extends JsonResource
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
            'name' => $this->Name,
//            'stages' => [
//                $this->learnerstage
//            ],
        ];
    }
}
