<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChallengeController extends Controller
{
    use GeneralTrait;
    //

    protected $userController;

    public function __construct(UserController $userController)
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
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $challenges = DB::table('challenges')->where('hero_instagram', $inputs['hero_instagram'])->where('hero_target', $inputs['hero_target'])->where('user_id', $inputs['user_id'])->get();

                if (!$challenges) {
                    return $this->returnError('', 'challenge not found');
                }

                return $this->returnData('challenge', $challenges[0]);
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
            'in_leader_board' => 'required|boolean',
            'is_verefied' => 'required|boolean',
            'user_id' => 'required|integer',
            'created_at' => 'required|string',
            'leaderBoardAdmin' => 'boolean|default:false',
        ];

        $validation = validator()->make($request->all(), $rules);

        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();

            try {
                if ($inputs['leaderBoardAdmin']) {
                    $result = DB::table('challenges')->where('id', $inputs['id'])-> update([
                        'id' => $inputs['id'],
                        'hero_instagram' => $inputs['hero_instagram'],
                        'hero_target' => $inputs['hero_target'],
                        'points' => $inputs['points'],
                        'in_leader_board' => $inputs['in_leader_board'],
                        'is_verefied' => $inputs['is_verefied'],
                        'created_at' => $inputs['created_at'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                else{
                    $result = DB::table('challenges')->where('id', $inputs['id'])->where('user_id', $inputs['user_id'])->update([
                        'id' => $inputs['id'],
                        'hero_instagram' => $inputs['hero_instagram'],
                        'hero_target' => $inputs['hero_target'],
                        'points' => $inputs['points'],
                        'in_leader_board' => $inputs['in_leader_board'],
                        'is_verefied' => $inputs['is_verefied'],
                        'created_at' => $inputs['created_at'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }


                if (!$result) {
                    return $this->returnData('updating', false);
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
            'user_id' => 'required|integer',
        ];

        $validation = validator()->make($request->all(), $rules);
        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs  = $request->input();
            try {
                $challenges = DB::table('challenges')->where('user_id', $inputs['user_id'])->get();

                if (count($challenges) > 0) {
                    return $this->returnData('challenge', $challenges[0]);
                } else {
                    return $this->returnError('', 'challenge not found !!');
                }
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }
    }

    public function getTrandingChallenges(Request $request)
    {
        $rules = [
            'last_id' =>'required|integer',
            'last_points' => 'required|integer',

        ];

        $validation = validator()->make($request->all(), $rules);
        if (!$validation) {
            return $this->returnValidationError($validation);
        } else {
            $inputs  = $request->input();

            try {

                if ($inputs['last_id'] > 0 && $inputs['last_points'] > 0) {
                    $challenges = DB::table('challenges')->where('in_leader_board', 1)->where('points', '<=', $inputs['last_points'])->where('id' , '!=' , $inputs['last_id'])->orderByDesc('points')->take(15)->get();
                } else {
                    $challenges = DB::table('challenges')->where('in_leader_board', 1)->orderByDesc('points')->take(15)->get();
                }


                if (!$challenges) {
                    return $this->returnError('', 'page not found');
                }

                return $this->returnData('challenges', $challenges);
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }
    }

    private function choosingTheChallengeTime()
    {
        $date = date('m');

        if ($date <= 2) {
            return date('Y-m-d', strtotime(date('Y') . '-2-1'));
        } else
        if ($date <= 5) {
            return date('Y-m-d', strtotime(date('Y') . '-5-1'));
        }
        if ($date <= 8) {
            return date('Y-m-d', strtotime(date('Y') . '-8-1'));
        }
        if ($date <= 11) {
            return date('Y-m-d', strtotime(date('Y') . '-11-1'));
        }
    }
}
