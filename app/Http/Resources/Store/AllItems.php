<?php

namespace App\Http\Resources\Store;

use App\Http\Resources\Series\SeriesCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class AllItems extends JsonResource
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
            'email'=>$this->email,
            'series'=>[
               SeriesCollection::Collection($this->series)
            ]
        ];
    }
}
