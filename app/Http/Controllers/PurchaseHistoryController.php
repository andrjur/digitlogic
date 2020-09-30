<?php

namespace App\Http\Controllers;

use App\PurchaseHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\UsersController as Users;

class PurchaseHistoryController extends Controller
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, array(
            'id_services' => 'required',
            'status' => 'required',
            'token' => 'required'
        ));

        $token = $request->input('token');

        $user = $this->users->checkUserExistByToken($token);

        if (empty($user)) {
            return response()->json(['status' => 'fail'], 404);
        }

        $userData = $user->attributesToArray();

        $dataSave = $request->all();
        $dataSave['id_user'] = $userData['id'];
        unset($dataSave['token']);

        $history = PurchaseHistory::create($dataSave);

        return response()->json($history, 201,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getAllPaid(Request $request)
    {
        $this->validate($request, array(
            'token' => 'required',
        ));

        $token = $request->input('token');

        $user = $this->users->checkUserExistByToken($token);

        if (empty($user)) {
            return response()->json(['status' => 'fail'], 404);
        }

        $userData = $user->attributesToArray();

        $result = response()->json(PurchaseHistory::where('id_user', $userData['id'])->
            where('status', 'paid')->get(), 200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
        return $result;
    }

}
