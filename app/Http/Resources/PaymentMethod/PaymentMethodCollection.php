<?php

namespace App\Http\Resources\PaymentMethod;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodCollection extends JsonResource
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
                'href' => [
                    // 'subject' => route('subject.show', $this->id)
                ],
            ];
        }

}
