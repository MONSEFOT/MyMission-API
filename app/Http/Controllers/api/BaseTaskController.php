<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BaseTask;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaseTaskController extends Controller
{
    use GeneralTrait;
    //

    public function createBaseTask(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'session_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                $result = DB::table('base_tasks')->insert([
                    'title' => $inputs['title'],
                    'session_id' => $inputs['session_id'],
                ]);

                if(!$result){
                    return $this->returnData('creating' , false);
                }
                return $this->returnData('creating' , true);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }

    public function updateBaseTaskPoints(Request $request){
        $rules = [
            'id' => 'required|integer',
            'points' => 'required|integer|max:2',
        ];

        $validation = validator()->make($request->all() , $rules);

        if(!$validation){
            return $this->returnValidationError($validation);
        }
        else{
            $inputs = $request->input();

            try {
                $base_task = BaseTask::find($inputs['id']);

                if(!$base_task){
                    return $this->returnError('' , 'Base task not found ');
                }

                if(($base_task->points + $inputs['points']) > 0){
                    $result = DB::table('base_tasks')->where('id', $inputs['id'])->update(['points' => ($base_task->points + $inputs['points']) , 'complete_state' => true]);

                    if(!$result){
                        return $this->returnData('baseTask' , 'base task  not fount');
                    }
                    $base_task = BaseTask::find($inputs['id']);


                    return $this->returnData('baseTask' , $base_task);
                }else if (($base_task->points + $inputs['points']) == 0){
                    $result = DB::table('base_tasks')->where('id', $inputs['id'])->update(['points' => ($base_task->points + $inputs['points']) , 'complete_state' => false]);

                    if(!$result){
                        return $this->returnData('baseTask' , 'base task  not fount');
                    }
                    $base_task = BaseTask::find($inputs['id']);


                    return $this->returnData('baseTask' , $base_task);
                }


            } catch (\Throwable $th) {
                return $this->returnError('' , $th->getMessage());
            }
        }
    }


    public function deleteBaseTasks(Request $request)
    {

        $rules = [
            'session_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all() , $rules);

        if(!$validation){
            return $this->returnValidationError($validation);
        }
        else{
            $inputs = $request->input();

            try {
                $result = BaseTask::where('session_id', $inputs['session_id'])->delete();

                if (!$result) {
                    return $this->returnError('' , 'deleting faild');
                }
                return $this->returnData('deleting' , true);
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }

    }

    public function getBaseTasks(Request $request)
    {
        $rules = [
            'session_id' => 'required|integer'
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                $basetasks = DB::table('base_tasks')->where('session_id', $inputs['session_id'])->get();

                if (!$basetasks) {
                    return $this->returnError('', 'Base tasks not found !');
                }
                return $this->returnData('base_tasks', $basetasks);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }
}
