<?php

namespace App\Http\Resources\PublisherServe;

use App\Http\Resources\Item\ItemCollection;
use App\Http\Resources\Learner\LearnerStageCollection;
use App\Http\Resources\Partner\PartnerCollection;
use App\Http\Resources\Subject\SubjectCollection;
use App\Http\Resources\User\AgentCollection;
use App\model\Agent;
use App\model\LearnerStage;
use App\model\Subject;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PublisherInitialize extends JsonResource
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
            'item' => [
                ItemCollection::collection($this->Item)
            ],
            'partners'=>[
                PartnerCollection::collection($this->agent)
            ],
            'agents'=>[
               AgentCollection::collection(Agent::all())
            ],
            'learnerstages'=>[
                LearnerStageCollection::collection(LearnerStage::all())
            ],
            'subjects'=>[
                SubjectCollection::collection(Subject::all())
            ],
            'approvedpartners'=>[
                PartnerCollection::collection($this->approvedagent)
            ]
        ];
    }
}
