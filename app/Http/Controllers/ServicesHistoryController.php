<?php

namespace App\Http\Controllers;

use App\ServiceHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\UsersController as Users;

class ServicesHistoryController extends Controller
{

    public $users;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->users = new Users;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        $serviceHistory = ServiceHistory::create($data);

        return $serviceHistory;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getAll(Request $request)
    {
        $this->validate($request, array(
            'token' => 'required'
        ));

        $token = $request->input('token');

        $user = $this->users->checkUserExistByToken($token);

        if (empty($user)) {
            return response()->json(['status' => 'fail'], 404);
        }

        $userData = $user->attributesToArray();

        $result = response()->json(ServiceHistory::where('id_user', $userData['id'])->get(), 200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
        return $result;
    }

}
