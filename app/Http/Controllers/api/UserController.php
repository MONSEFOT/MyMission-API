<?php

namespace App\Http\Controllers\api;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

/*
 *This is the controller for each account in the database (users table).
 *Each controller function represents a specific function in the database.
 *Note: You can visit the .env file for more data about the database.
 *Functions:

    *register:{
        The registration will allow the program that works through the API ,
        to copy the user's Google account information and send it to be saved in the data leader.
    }

    *login :{
        Logging in is done with the email address and password, and if the information sent is correct,
        a token will be returned as a value in the response,
        Which allows the user to perform the rest of the operations that need authentication with a token
    }

    *updateUser:{
        In updating the user, the update values are received in the function from these values the user ID,
        which allows the program to identify the account that should be updated in the database.
    }

    *deleteUser:{
        Simply delete a user The user to be deleted is specified using the ID received by the function,
        and the deletion takes place in the database.
    }

    *roleValidator:{
        This function is a private function that works only inside the controller,
        and its work is to know the rank of the user, whether he is:
          *owner,
          *admin,
          *hero
    }

    *findUserWithToken:{
        The user can be returned in a number of ways,
        including the method of the token that is received as a parameter for the function in the request,
        and the user who owns the token is returned
    }

    *searchForUser:{
        The search function on the user works with the same mechanism as the previous function,
        but it receives the ID of the receiver as a parameter in the request.
    }
*/

class UserController extends Controller
{
    //
    use GeneralTrait;

    public function login(Request $request)
    {
        try {
            $creds = $request->only(['email', 'password']);

            $token = Auth::attempt($creds);

            if (!$token) {
                return $this->returnError('E001', 'User not Found');
            }

            return $this->returnData('token', $token, 'The response was successful');
        } catch (\Throwable $th) {
            return $this->returnError('E001', $th->getMessage());
        }
    }

    public function register(Request $request)
    {
        $rules = [
            'display_name' => 'required|string|min:5|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|max:255',
        ];

        $validation = validator()->make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();


            try {

                $result = DB::table('users')->insert([
                    'display_name' => $inputs['display_name'],
                    'email' => $inputs['email'],
                    'password' => bcrypt($inputs['password']),
                    'role' => 'hero',
                    ]);



                return $this->returnData('register', $result);
            } catch (\Throwable $th) {
                return $this->returnError("", $th->getMessage());
            }
        }
    }

    public function updateUser(Request $request)
    {

        $rules = [
            'id' => 'required|integer',
            'display_name' => 'required|string|min:5|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|max:255',
            'role' => 'string',
        ];

        $validation = validator()->make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();


            try {


                $result = DB::table('users')->where('id' , $inputs['id'])->update([
                    'id' => $inputs['id'],
                    'display_name' => $inputs['display_name'],
                    'email' => $inputs['email'],
                    'password' => bcrypt($inputs['password']),
                    'role' => $inputs['role'],
                    ]);



                return $this->returnData('update', $result);
            } catch (\Throwable $th) {
                return $this->returnError("", $th->getMessage());
            }
        }
    }

    public function deleteUser(Request $request)
    {
        $rules = [

            'user_id' => 'required|integer'
        ];

        $validation = validator()->make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();
            try {
                $deletion = DB::table('users')->where('id' , $inputs['user_id'])->delete();

                if (!$deletion) {
                    return $this->returnError('', 'deletion failed');
                }

                return $this->returnData('deletion', 'Seccessful' . $deletion);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }

    private function roleValidator(Request $request)
    {
        $user = $this->findUserWithToken($request);

        try {

            switch ($user->role) {
                case 'owner':
                    return $user->role;
                    break;
                case 'admin':
                    return $user->role;
                    break;
                case 'hero':
                    return $user->role;
                default:
                    return 'no role';
            }
        } catch (\Throwable $th) {
            return $this->returnError('E003', $th->getMessage());
        }
    }

    public function findUserWithToken(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->returnError('E003', 'User not found');
            }

            return $user;
        } catch (\Throwable $th) {
            return $this->returnError('E003', $th->getMessage());
        }
    }

    public function searchForUser(Request $request)
    {

        $rules = [
            'user_id' => 'required|integer'
        ];

        $validation = validator()->make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->returnValidationError($validation);
        } else {
            $inputs = $request->input();
            try {
                $user = DB::table('users')->where('id', '=', $inputs['user_id'])->get();

                if (!$user) {
                    return $this->returnError('', 'User not found');
                }

                return $this->returnData('user', $user);
            } catch (\Throwable $th) {
                return $this->returnError('', $th->getMessage());
            }
        }
    }
}
