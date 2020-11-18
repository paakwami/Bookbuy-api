<?php

namespace App\Http\Controllers;

use App\Helpers\Sms\Maker;
use App\Http\Resources\Learner\LearnerStageCollection;
use App\Http\Resources\Learner\LearnerStageResource;
use App\model\LearnerStage;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function storeLearnerStage(Request $request){
        $request->validate([
            'name'=>'required|string|max:255',
        ]);

        $learnerstage = LearnerStage::where('name',$request->get('name'))->first();

        if(!$learnerstage){

            $learnerstage = new LearnerStage([
                'name' => $request->get('name'),
            ]);
            $learnerstage->save();
            return response(new LearnerStageResource($learnerstage), 201);
        }
        return response('Class already Exist', 302);
    }
    public function showAllLearnerStage()
    {
        return LearnerStageCollection::collection(LearnerStage::paginate(10)) ;
    }

    public function trysms(){
        $message = "hello working";
        return Maker::sendSms('bookbuy', '0264000040', $message);

    }
}
