<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChallengeController extends Controller
{
    use GeneralTrait;
    //

    protected $userController;

    public function __construct(UserController $userController, SessionController $sessionController)
    {
        $this->userController = $userController;
    }

    public function createChallenge(Request $request)
    {

        $rules = [
            'hero_instagram' => 'required|unique|string',
            'hero_target' => 'required|string|min:5|max:255',
            'user_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                DB::table('challenges')->insert([
                    'hero_instagram' => $inputs['hero_instagram'],
                    'hero_target' => $inputs['hero_target'],
                    'user_id' => $inputs['user_id'],
                ]);

                $challenge = DB::table('challenges')->where('hero_instagram', $inputs['hero_instagram'])->where('hero_target', $inputs['hero_target'])->where('user_id', $inputs['user_id'])->get();
                
                if(!$challenge){
                    return $this->returnError('' , 'challenge not found');
                }

                return $this->returnData('challenge', $challenge[0]);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }

    public function updateChallenge(Request $request)
    {

        $rules = [
            'id' => 'required|integer',
            'hero_instagram' => 'required|string',
            'hero_target' => 'required|string',
            'points' => 'required|integer|max:2', 
            'in_leader_board' => 'required|string',
            'is_verefied' => 'required|string',
            'user_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                $result = DB::table('challenges')->where('id', $inputs['id'])->where('user_id', $inputs['user_id'])->update([
                    'id' => $inputs['id'],
                    'hero_instagram' => $inputs['hero_instagram'],
                    'hero_target' => $inputs['hero_target'],
                    'points' => $inputs['points'],
                    'in_leader_board' => ($inputs['in_leader_board'] == "true")? true : false,
                    'is_verefied' => ($inputs['is_verefied'] == "true")? true : false,
                ]);
                
                if(!$result){
                    return $this->returnData('updating' , false);
                }
                
                return $this->returnData('updating', true);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }

    public function deleteChallenge(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
            'user_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                $result = DB::table('challenges')->where('id', $inputs['id'])->where('user_id', $inputs['user_id'])->delete();

                if (!$result) {
                    return $this->returnData('deletion', false);
                }
                return $this->returnData('deletion', true);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }

    public function getChallenge(Request $request)
    {

        $rules = [
            'challenge_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);
        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs  = $request->input();
            try {
                $challenges = DB::table('challenges')->where('id', $inputs['challenge_id'])->where('user_id', $inputs['user_id'])->get();

                if (!$challenges) {
                    $this->returnError('' , 'challenge not found');
                }
                return $this->returnData('challenge' , $challenges[0]);
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }
    }
    
    public function getTrandingChallenges(Request $request){
        $rules = [
            'last_id' => 'required|integer',
            
        ];
        
         $validation = validator()->make($request->all(), $rules);
        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs  = $request->input();
            try {
                $challenges = DB::table('challenges')->where('id'  , '>' , $inputs['last_id'])->where('id' , '<=' , $inputs['last_id'] + 15)->where('in_leader_board' , true)->get();
                
                if(!$challenges){
                    return $this->returnError('' , 'page not found');
                }
                
                return $this->returnData('challenges' , $challenges);

            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }
    } 

}
