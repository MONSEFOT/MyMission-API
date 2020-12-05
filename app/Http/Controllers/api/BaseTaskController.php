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
    //creating process is just with the baseTask's title and the referance id of session
    public function createBaseTask(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'session_id' => 'required|integer',
        ];

        //validation to find index of title and index of session for complete the creating
        $validation = validator()->make($request->all(), $rules);

        //validation condition
        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {

            //add the following baseTask info into array called @inputs
            $inputs = $request->input();

            try {

                //insert data into base_@tasks_tasble in myssql database
                $result = DB::table('base_tasks')->insert([
                    'title' => $inputs['title'],
                    'session_id' => $inputs['session_id'],
                ]);

                //cheking inserting results
                if(!$result){

                    //returning error in the response when the result is equale to null
                    return $this->returnError('' , 'Process failed !!');
                }

                //returning true in the response when the result isn't equale to null
                return $this->returnData('creating' , true);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }

    //in the baseTask the iformation of each task are fixed , just you can update the points with the complete status
    public function updateBaseTaskPoints(Request $request){
        $rules = [
            'id' => 'required|integer',
            'points' => 'required|integer|max:2|min:-2',
        ];

        $validation = validator()->make($request->all() , $rules);

        if(!$validation){
            return $this->returnValidationError($validation);
        }
        else{
            $inputs = $request->input();

            try {
                $base_tasks = DB::table('base_tasks')->where('id' , $inputs['id'])->get();

                if(!$base_tasks[0]){
                    return $this->returnError('' , 'Base task not found ');
                }

                if(($base_tasks[0]->points + $inputs['points']) > 0){
                    $result = DB::table('base_tasks')->where('id', $inputs['id'])->update(['points' => ($base_tasks[0]->points + $inputs['points']) , 'complete_state' => true]);

                    if(!$result){
                        return $this->returnError('' , 'Not successful update');
                    }
                    $base_tasks = DB::table('base_tasks')->where('id', $inputs['id'])->get();

                    $sessions = DB::table('sessions')->where('id' , $base_tasks[0]->session_id)->get();

                    if(!$sessions){
                        return $this->returnError('' , 'Session not found' );
                    }

                    if($sessions[0]->points + $inputs['points'] > 0){
                        $result = DB::table('sessions')->where('id' , $sessions[0]->id)->update(['points' => ($sessions[0]->points + $inputs['points']) , 'complete_state' => true ]);
                    }else{
                        $result = DB::table('sessions')->where('id' , $sessions[0]->id)->update(['points' => ($sessions[0]->points + $inputs['points']) , 'complete_state' => false ]);
                    }

                    $challenges = DB::table('challenges')->where('id' , $sessions[0]->challenge_id)->get();

                    if(!$challenges){
                        return $this->returnError('' , 'Challenge not found' );
                    }

                    $result = DB::table('challenges')->where('id' , $challenges[0]->id)->update(['points' => ($challenges[0]->points + $inputs['points'])]);

                    return $this->returnData('baseTask' , $base_tasks[0]);
                }else if (($base_tasks[0]->points + $inputs['points']) == 0){
                    $result = DB::table('base_tasks')->where('id', $inputs['id'])->update(['points' => ($base_tasks[0]->points + $inputs['points']) , 'complete_state' => false]);

                    if(!$result){
                        return $this->returnError('' , 'Not successful update');
                    }

                    $base_tasks = DB::table('base_tasks')->where('id', $inputs['id'])->get();

                    $sessions = DB::table('sessions')->where('id' , $base_tasks[0]->session_id)->get();

                    if(!$sessions){
                        return $this->returnError('' , 'Session not found' );
                    }

                    if($sessions[0]->points + $inputs['points'] > 0){
                        $result = DB::table('sessions')->where('id' , $sessions[0]->id)->update(['points' => ($sessions[0]->points + $inputs['points']) , 'complete_state' => true ]);
                    }else{
                        $result = DB::table('sessions')->where('id' , $sessions[0]->id)->update(['points' => ($sessions[0]->points + $inputs['points']) , 'complete_state' => false ]);
                    }

                    $challenges = DB::table('challenges')->where('id' , $sessions[0]->challenge_id)->get();

                    if(!$challenges){
                        return $this->returnError('' , 'Challenge not found' );
                    }

                    $result = DB::table('challenges')->where('id' , $challenges[0]->id)->update(['points' => ($challenges[0]->points + $inputs['points'])]);

                    return $this->returnData('baseTask' , $base_tasks[0]);
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
