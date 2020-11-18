<?php

namespace App\Http\Resources\Region;

use App\Http\Resources\District\DistrictResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
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
            'district' => [
                DistrictResource::Collection($this->district)
            ],
        ];
    }
}
