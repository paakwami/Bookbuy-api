<?php

namespace App\Http\Controllers;

use App\Http\Resources\Classgroup\ClassgroupCollection;
use App\model\classgroup;
use App\model\LearnerStage;
use Illuminate\Http\Request;

class ClassgroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ClassgroupCollection::collection(classgroup::all()) ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
        ]);
        $group = classgroup::where('name',$request->get('name'))->first();

        if(!$group){
            $items = $request->get('class');

            $group = new classgroup([
                'name' => $request->get('name'),
            ]);
            $group->save();
            foreach ($items as $i){
                $learnerstage = LearnerStage::find($i['id']);
                $group->LearnerStage()->attach($learnerstage);
            }
           return ClassgroupCollection::make($group);
        }
        return response('Group already Exist', 302);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\classgroup  $classgroup
     * @return \Illuminate\Http\Response
     */
    public function show(classgroup $classgroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\classgroup  $classgroup
     * @return \Illuminate\Http\Response
     */
    public function edit(classgroup $classgroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\classgroup  $classgroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, classgroup $classgroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\classgroup  $classgroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(classgroup $classgroup)
    {
        //
    }
}
