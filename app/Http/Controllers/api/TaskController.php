<?php
/*
   Documentation
 */

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\UserController;
use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    use GeneralTrait;

    //
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->userController = $userController;
    }


    public function createTask(Request $request)
    {
        $user = $this->userController->findUserWithToken($request);

        $rules = [
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:5|max:255',
        ];

        $validation = validator()->make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();


            try {


                DB::table('tasks')->insert([
                    'title' => $inputs['title'],
                    'description' => $inputs['description'],
                    'user_id' => $user->id,
                ]);

                $task = DB::table('tasks')->where('title' , $inputs['title'])->where('description' , $inputs['description'])->where('user_id', $user->id)->get();



                return $this->returnData('task', $task);
            } catch (\Throwable $th) {
                return $this->returnError("", $th->getMessage());
            }
        }
    }

    public function updateTask(Request $request)
    {
        $user = $this->userController->findUserWithToken($request);

        $rules = [
            'id' => 'required|integer',
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:5|max:255',
            'complete_state' => 'nullable|boolean'
        ];

        $validation = validator()->make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();


            try {


                DB::table('tasks')->where('id' , $inputs['id'])->update([
                    'id' => $inputs['id'],
                    'title' => $inputs['title'],
                    'description' => $inputs['description'],
                    'complete_state' => $inputs['complete_state'],
                    'user_id' => $user->id,
                ]);

                $task = DB::table('tasks')->where('id' , $inputs['id'])->get();


                return $this->returnData('task', $task);
            } catch (\Throwable $th) {
                return $this->returnError("", $th->getMessage());
            }
        }
    }

    public function delete(Request $request)
    {
        $user = $this->userController->findUserWithToken($request);

        $rules = [
            'task_id' => 'required|integer',

        ];

        $validation = validator()->make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {

                $deletion = DB::table('tasks')->where('id', $inputs['task_id'])->where('user_id', $user->id)->delete();

                return $this->returnData('deletion', 'Seccessful' . $deletion);
            } catch (\Throwable $th) {
                return $this->returnError("", $th->getMessage());
            }
        }
    }

    public function getUserTasks(Request $request)
    {

        $user = $this->userController->findUserWithToken($request);

        $rules = [
            'last_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                $tasks = DB::table('tasks')->whereBetween('id', [$inputs['last_id'] , $inputs['last_id'] + 16])->where('user_id', '=', $user->id)->get();
                if (!$tasks)
                    return $this->returnError("", "tasks not found !");
                return $this->returnData('tasks', $tasks);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }
}
