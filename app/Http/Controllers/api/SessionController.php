<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\UserController;
use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Session;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    use GeneralTrait;
    //


    public function createSession(Request $request)
    {

        $rules = [
            'number' => 'required|integer',
            'unLock_date' => 'required|string',
            'challenge_id' => 'required|integer',
            'week_number' => 'required|integer|min:1'
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                DB::table('sessions')->insert([
                    'number' => $inputs['number'],
                    'unLock_date' => $inputs['unLock_date'],
                    'challenge_id' => $inputs['challenge_id'],
                    'week_number' => $inputs['week_number']
                ]);


                $session = DB::table('sessions')->where('number', $inputs['number'])->where('unLock_date', $inputs['unLock_date'])->where('challenge_id', $inputs['challenge_id'])->get();

                return $session;
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }


    public function deleteSession(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                $result = DB::table('sessions')->where('id', $inputs['id'])->delete();

                if (!$result) {
                    return $this->returnError('', 'deletion failed');
                }
                return $this->returnData('deletion', 'Seccessful' . $result);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }

    public function deleteAllSessions(Request $request)
    {
        $rules = [
            'challenge_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                $result = DB::table('sessions')->where('challenge_id', $inputs['challenge_id'])->delete();

                if (!$result) {
                    return $this->returnError('', 'deletion failed');
                }
                return $this->returnData('deletion', 'Seccessful' . $result);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }


    public function getAllSessions(Request $request)
    {
        $rules = [
            'challenge_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                $sessions[] = DB::table('sessions')->where('challenge_id', $inputs['challenge_id'])->get();

                if (!$sessions) {
                    return $this->returnError('', 'no sessions founed');
                }

                return $this->returnData('sessions', $sessions);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }
    
    public function updateSessionPoints(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
            'points' => 'required|integer|max:2'
        ];

        $validation = validator()->make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                $session = Session::find($inputs['id']);
                $accoundingSession_id = Session::where('week_number', $session->week_number)->where('number', 7)->value('id');

                    $accountingSession = Session::find($accoundingSession_id);
                if (!$session) {
                    return $this->returnError('', 'session not found');
                }

                if ($session->points + $inputs['points'] > 0) {
                    DB::table('sessions')->where('id', $inputs['id'])->update(['points' => ($session->points + $inputs['points']), 'complete_state' => true]);





                    if ($accountingSession->points + $inputs['points'] > 0) {


                        DB::table('sessions')->where('week_number', $session->week_number)->where('number', 7)->update(['points' => ($accountingSession->points + $inputs['points']), 'complete_state' => true]);



                        return $this->returnData('updating', 'Seccessful');
                    } else if($accountingSession->points + $inputs['points'] == 0){

                        DB::table('sessions')->where('week_number', $session->week_number)->where('number', 7)->update(['points' => ($accountingSession->points + $inputs['points']), 'complete_state' => false]);

                        return $this->returnData('updating', 'Seccessful');
                    }
                } else if($session->points + $inputs['points'] == 0) {


                    DB::table('sessions')->where('id', $inputs['id'])->update(['points' => ($session->points + $inputs['points']), 'complete_state' => false]);



                    if ($accountingSession->points + $inputs['points'] > 0) {


                        DB::table('sessions')->where('week_number', $session->week_number)->where('number', 7)->update(['points' => ($accountingSession->points + $inputs['points']), 'complete_state' => true]);

                        return $this->returnData('updating', 'Seccessful');
                    } else if($accountingSession->points + $inputs['points'] == 0){


                        DB::table('sessions')->where('week_number', $session->week_number)->where('number', 7)->update(['points' => ($accountingSession->points + $inputs['points']), 'complete_state' => false]);

                        return $this->returnData('updating', 'Seccessful');
                    }
                }
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }
    }
}
