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
        $user = $this->userController->findUserWithToken($request);

        $rules = [
            'hero_instagram' => 'required|unique|string',
            'hero_target' => 'required|string|min:5|max:255'
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
                    'user_id' => $user->id,
                ]);

                $challenge = DB::table('challenges')->where('hero_instagram', $inputs['hero_instagram'])->where('hero_target', $inputs['hero_target'])->where('user_id', $user->id)->get();

                return $this->returnData('challenge', $challenge);
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
            'user_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                DB::table('challenges')->where('id', $inputs['id'])->where('user_id', $inputs['user_id'])->update([
                    'id' => $inputs['id'],
                    'hero_instagram' => $inputs['hero_instagram'],
                    'hero_target' => $inputs['hero_target'],
                ]);

                $challenge = DB::table('challenges')->where('id', $inputs['id'])->where('user_id', $inputs['user_id'])->get();

                return $this->returnData('challenge', $challenge);
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
                    return $this->returnError('', 'deletion failed');
                }
                return $this->returnData('deletion', 'Seccessful' . $result);
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
                $challenge = DB::where('id', $inputs['challenge_id'])->where('user_id', $inputs['user_id'])->get();

                if (!$challenge) {
                    return false;
                }
                return $challenge;
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }
    }

    public function updateChallengePoints(Request $request)
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
                $challenge = Challenge::find($inputs['id']);
                if (!$challenge) {
                    return $this->returnError('', 'challenge not found');
                }
                DB::table('challenges')->where('id', $inputs['id'])->update(['points' => ($challenge->points + $inputs['points']),]);

                return $this->returnData('updating', 'Seccessful');
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }
    }
}
