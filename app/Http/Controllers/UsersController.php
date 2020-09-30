<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Users;

class UsersController extends Controller
{

    public function __construct()
    {
        //  $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function login(Request $request)
    {
        if ($request->input('token')) {
            $user = $this->checkUserExistByToken($request->input('token'));
            $userData = $user->attributesToArray();

            if (empty($user)) {
                return response()->json(['status' => 'fail'], 404);
            }

            return response()->json(['status' => 'success', 'token' => $userData['api_key']]);
        }

        $user = Users::where('email', $request->input('email'))->first();

        if (Hash::check($request->input('password'), $user->password)) {
            $token = base64_encode($request->input('password') .
                $request->input('email') . random_bytes(40)
            );

            Users::where('email', $request->input('email'))->update(['api_key' => $token]);
            return response()->json(['status' => 'success', 'token' => $token]);
        } else {
            return response()->json(['status' => 'fail'], 401);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
//            'phone' => 'required',
            'sex' => 'required',
//            'birthdate' => 'required',
            'password' => 'required',
        ]);

        $user = Users::where('email', $request->input('email'))->first();

        try {
            if (empty($user)) {
                $user = new Users;
                $user->first_name = $request->input('first_name');
                $user->last_name = $request->input('last_name');
                $user->email = $request->input('email');
                $user->phone = $request->input('phone');
                $user->sex = $request->input('sex');
                $user->birthdate = date('Y-m-d', strtotime($request->input('birthdate')));
                $plainPassword = $request->input('password');
                $user->password = Hash::make($plainPassword);

                $token = base64_encode($request->input('password') .
                    $request->input('email') . random_bytes(40)
                );
                $user->api_key = $token;

                $user->save();

                //return successful response
                return response()->json(['user' => $user, 'message' => 'CREATED'], 201);
            } else {
                return response()->json(['message' => 'User Registration Failed!'], 409);
            }
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }

    }

    /**
     * @param $token
     * @return bool
     */
    public function checkUserExistByToken($token)
    {
        try {
            return Users::where('api_key', $token)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
        ]);


        $user = $this->checkUserExistByToken($request->input('token'));

        if (empty($user)) {
            return response()->json(['status' => 'fail'], 404);
        }

        $userData = $user->attributesToArray();

        try {
            if (!empty($request->input('first_name'))) {
                $user->first_name = $request->input('first_name');
            }
            if (!empty($request->input('last_name'))) {
                $user->last_name = $request->input('last_name');
            }
            if (!empty($request->input('email'))) {
                $user->email = $request->input('email');
            }
            if (!empty($request->input('phone'))) {
                $user->phone = $request->input('phone');
            }
            if (!empty($request->input('sex'))) {
                $user->sex = $request->input('sex');
            }
            if (!empty($request->input('first_name'))) {
                $user->birthdate = date('Y-m-d', strtotime($request->input('first_name')));
            }
            if (!empty($request->input('password'))) {
                $plainPassword = $request->input('password');
                $user->password = Hash::make($plainPassword);
            }

            $token = base64_encode($request->input('password') .
                $request->input('email') . random_bytes(40)
            );
            $user->api_key = $token;

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'Update'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User update Failed'], 400);
        }
    }
}

