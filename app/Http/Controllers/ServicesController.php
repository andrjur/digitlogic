<?php

namespace App\Http\Controllers;

use App\Service;
use function Faker\Provider\pt_BR\check_digit;
use Illuminate\Http\Request;
use App\Http\Controllers\UsersController as Users;
use App\Http\Controllers\ServicesHistoryController as ServiceHistory;
use Symfony\Component\Console\Helper\Helper;

class ServicesController extends Controller
{

    public $users;
    public $serviceHistory;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->users = new Users;
        $this->serviceHistory = new ServiceHistory;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function serviceMindNumber(Request $request)
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

        $birthDate = $userData['birthdate'];
        $birthDay = date('d', strtotime($birthDate));

        $mindNumber = $this->calculateSumNumber($birthDate);
        $missionNumber = $this->calculateSumNumber($birthDay);

        $missions = $this->readJsonFile('missions.json');
        $minds = $this->readJsonFile('minds.json');

        $result = array(
            'mindNumber' => array(
                $mindNumber => $minds[$mindNumber]
            ),
            'missionNumber' => array(
                $missionNumber => $missions[$missionNumber]
            ),
            'birthDate' => $birthDate
        );

        $data = array(
            'id_user' => $userData['id'],
            'name_service' => 'MindNumber',
            'value' => $birthDate,
            'result' => json_encode($result, JSON_UNESCAPED_UNICODE),
        );
        $this->saveHistory($data);

        return response()->json(['message' => 'Success!', 'result' => $result], 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function serviceLifeMatrix(Request $request)
    {
        $this->validate($request, array(
            'token' => 'required'
        ));

        $token = $request->input('token');

        $user = $this->users->checkUserExistByToken($token);

        if (empty($user)) {
            return response()->json(['status' => 'User is not found.'], 404);
        }

        $userData = $user->attributesToArray();

        $birthDate = $userData['birthdate'];
        $birthDay = date('d', strtotime($birthDate));

        $mindNumber = $this->calculateSumNumber($birthDate);
        $missionNumber = $this->calculateSumNumber($birthDay);

        $result = array(
            'numbers' => str_split($birthDate),
            'mindNumber' => $mindNumber,
            'missionNumber' => $missionNumber,
        );

        $result = $this->_serviceLifeMatrix($result);

        $result['birthDate'] = $birthDate;

        $data = array(
            'id_user' => $userData['id'],
            'name_service' => 'LifeMatrix',
            'value' => $birthDate,
            'result' => json_encode($result, JSON_UNESCAPED_UNICODE),
        );

        $this->saveHistory($data);

        return response()->json(['message' => 'Success!', 'result' => $result], 200);
    }

    /**
     * @param $data
     * @return array
     */
    private function _serviceLifeMatrix($data)
    {
        $numbers = $data['numbers'];
        $mindNumber = $data['mindNumber'];
        $missionNumber = $data['missionNumber'];

        $numbers[] = $mindNumber;
        $numbers[] = $missionNumber;

        $matrix = array(
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
        );

        foreach ($numbers as $number) {
            if (is_numeric($number) && $number != 0) {
                $matrix[(int)$number]++;
            }
        }

        $spheres = $this->readJsonFile('spheres.json');

        foreach ($spheres as $key => $sphere) {
            foreach ($sphere['variants'] as $k => $variant) {
                if ($this->checkCondition(array('conditions' => $variant['conditions'], 'matrix' => $matrix))) {
                    $result[$key][] = $variant['result'];
                }
            }
        }

        return array('result' => $result, 'matrix' => $matrix);
    }

    private function checkCondition($data)
    {
        $conditions = $data['conditions'];
        $matrix = $data['matrix'];

        foreach ($conditions as $key => $condition) {
            if ($matrix[$key] == $condition) {
                $result = true;
            } else {
                return false;
            }
        }

        return $result;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function serviceMatrix12SpheresLife(Request $request)
    {
        $this->validate($request, array(
            'token' => 'required'
        ));

        $token = $request->input('token');

        $user = $this->users->checkUserExistByToken($token);

        if (empty($user)) {
            return response()->json(['status' => 'User is not found.'], 404);
        }

        $userData = $user->attributesToArray();

        $birthDate = $userData['birthdate'];
        $birthDay = date('d', strtotime($birthDate));
        $birthM = date('m', strtotime($birthDate));
        $birthY = date('y', strtotime($birthDate));

        $mindNumber = $this->calculateSumNumber($birthDate);
        $missionNumber = $this->calculateSumNumber($birthDay);

        $matrix = array(
            0 => array(
                0 => $birthM, //A
                1 => $birthDay, //B
                2 => $birthY, //C
                3 => $mindNumber, //D
            ),
            1 => array(
                0 => $birthY - 2,
                1 => $mindNumber + 2,
                2 => $birthM - 2,
                3 => $birthDay + 2,
            ),
            2 => array(
                0 => $mindNumber + 1,
                1 => $birthY + 1,
                2 => $birthDay - 1,
                3 => $birthM - 1,
            ),
            3 => array(
                0 => $birthDay + 1,
                1 => $birthM - 3,
                2 => $mindNumber + 3,
                3 => $birthY - 1,
            ),
        );

        $count = count($matrix);

        foreach ($matrix as $key => $items) {
            if ($key == 0) {
                continue;
            }
            foreach ($items as $k => $item) {
                if ($item < 0) {
                    continue;
                }
                $matrix[$key][$k] = $this->calculateSumNumber($item);
            }
        }

        $sumLines = array();
        foreach ($matrix as $item) {
            $sumLines[] = $this->calculateSumNumber(array_sum($item));
        }

        $sumColumns = array();
        for ($j = 0; $j < $count; $j++) {
            $sum = 0;
            for ($i = 0; $i < $count; $i++) {
                $sum += $matrix[$i][$j];
            }
            $sumColumns[] = $this->calculateSumNumber($sum);
        }

        $sumMainDiag = array();

        for ($i = 0; $i < $count; $i++) {
            $sumMainDiag[] = $matrix[$i][$i];
        }

        $sumMainDiag = $this->calculateSumNumber(array_sum($sumMainDiag));

        $sumDiag = array();

        for ($i = 0, $j = $count - 1; $i < $count; $i++, $j--) {
            $sumDiag[] = (int)$matrix[$i][$j];
        }

        $sumDiag = $this->calculateSumNumber(array_sum($sumDiag));

        $result = array(
            'matrix' => $matrix,
            'sumLines' => $sumLines,
            'sumColumns' => $sumColumns,
            'sumMainDiag' => $sumMainDiag,
            'sumDiag' => $sumDiag,
        );

        $data = array(
            'id_user' => $userData['id'],
            'name_service' => 'Matrix12',
            'value' => $birthDate,
            'result' => json_encode($result, JSON_UNESCAPED_UNICODE),
        );

        $this->saveHistory($data);

        return response()->json(['message' => 'Success!', 'result' => $result], 200);
    }

    /**
     * @param $date
     * @return float|int
     */
    private function calculateSumNumber($date)
    {
        $numbers = str_split($date);
        $sum = array_sum($numbers);

        if (count(str_split($sum)) > 1) {
            return $this->calculateSumNumber($sum);
        }

        return $sum;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function readJsonFile($path)
    {
        $path = resource_path($path);

        return json_decode(file_get_contents($path), true);
    }

    /**
     * @param $data
     */
    private function saveHistory($data)
    {
        $this->serviceHistory->create($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getAll(Request $request)
    {
        $result = response()->json(Service::where('status', 1)->get(), 200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE);
        return $result;
    }
}
