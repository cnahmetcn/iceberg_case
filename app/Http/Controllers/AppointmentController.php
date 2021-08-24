<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class AppointmentController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse|object
     */

    public function store(Request $request)
    {

                // do request to check if postcode is right and if right get long address
                $response = $this->extractAddress($request->address);
                if ($response === 0) {
                    return $this->response->fail(['message' => __("response.InvalidPostcode")]);
                }
                $request['longAddress'] = $response;
                // do request to get distance and duration

                $response = $this->calcDistanceAndDuration($request->address, $this->postCode, $request->time);
                $request['distance'] = $response['distance'];
                $request['time'] = $response['time'];
                $request['checkoutTime'] = $response['checkoutTime'];
                $request['returnTime'] = $response['returnTime'];

                // create Appointment
                Appointment::create($request->all());

                return $this->response->success(['message' => "Appointment created succesfully"]);

    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|object
     */

    public function update(Request $request, $id)
    {
                                   // find Appointment  to update
                $appointment = Appointment::find($id);
                if (!empty($appointment)) {
                    // do request to check if postcode is right and if right get long address
                    $response = $this->extractAddress($request->address);
                    if ($response === 0) {
                        return $this->response->fail(['message' => "Invalid post code"]);
                    }
                    $request['longAddress'] = $response;
                    // do request to get distance and duration

                    $response = $this->calcDistanceAndDuration($request->address, $this->postCode, $request->time);
                    $request['distance'] = $response['distance'];
                    $request['time'] = $response['time'];
                    $request['checkoutTime'] = $response['checkoutTime'];
                    $request['returnTime'] = $response['returnTime'];
                    $request['created_by'] = Auth::guard('api')->user()->id;

                    // update Appointment
                    $appointment->update($request->all());
                    return $this->response->success(['message' => "Appointment updated succesfully"]);

                return $this->response->fail("Appointment updated failed");
                    }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|object
     */

    //this Func will delete one Appointment byID

    public function destroy($id)
    {
        $res = Appointment::where('id', $id)->delete();
        return $this->response->success(['message' => "Appointment delete succesfully"]);

    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|object
     */

    public function show($id)
    {
        return Appointment::find($id);
    }

    public function all()
    {
        return Appointment::all();
    }


    public function pagination(Request $request)
    {
            try {
                $appointments = DB::table('appointments');
                if (isset($request->date)  && !empty($request->date)) {
                    $appointments->where('appointments.date', '=', $request->date);
                }
                if (isset($request->created_by)  && !empty($request->created_by)) {
                    $appointments->where('appointments.created_by', '=', $request->created_by);
                }
                $appointments->where('appointments.hold', '=', $request->hold)
                    ->where('appointments.done', 'like', '%' . $request->done . '%')
                    ->where(function ($query) use ($request) {
                        $query->Where('appointments.email', 'like', '%' . $request->searchValue . '%')
                            ->orWhere('appointments.surname', 'like', '%' . $request->searchValue . '%')
                            ->orWhere('appointments.name', 'like', '%' . $request->searchValue . '%')
                            ->orWhere('appointments.address', 'like', '%' . $request->searchValue . '%')
                            ->orWhere('appointments.phone', 'like', '%' . $request->searchValue . '%');
                    });

                $appointments = $appointments->paginate($request->perPage, ['*'], 'appointments', $request->page)->toArray();

                // re decorator data by removing unneeded keys from json
                $appointments = $this->removePagination($appointments);
                $total = $appointments[1];
                $appointments = $appointments[0];

                return $this->response->success(["data" => $appointments, "numberOfPage" => $total, "count" => count($appointments)]);
            } catch (\Illuminate\Database\QueryException  $exception) {
                return $this->response->fail(["asd" => $exception]);
            }

    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|object
     */

    public function done(Request $request, $id)
    {
        // checkRequest Func checking requests
            try {
                $appointment = Appointment::find($id);
                // find Appointment  to update
                if (!empty($appointment)) {
                    $appointment->update(["done" => 1]);
                    return $this->response->success(['message' => "Appointment status change"]);
                }
                return $this->response->fail("Appointment status dont change");
            } catch (\Illuminate\Database\QueryException  $exception) {
                return $this->response->fail(['message' => __("response.DatabaseError")]);
            }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|object
     */

    public function assignment(Request $request, $id)
    {
            try {
                $appointment = Appointment::find($id);
                if (empty($appointment)) {
                    return $this->response->fail(['message' => __("response.AppointmentSelectionFail")]);
                }

                $freeUser = Appointment::where('hold', '=', 0)
                ->where(function ($query) use ($request, $appointment) {
                    $query->where('returnTime', '>=', $appointment->checkoutTime)
                        ->Where('checkoutTime', '<=', $appointment->checkoutTime);
                    $query->orWhere(function ($query) use ($request, $appointment) {
                        $query->where('returnTime', '>=', $appointment->returnTime)
                            ->Where('checkoutTime', '<=', $appointment->returnTime);
                    });
                })->where('created_by', $request->created_by)
                    ->where('done', '=', 0)
                    ->first();

                if (!empty($freeUser)) {
                    return $this->response->fail("This User is not available during these hours");
                }

                if (!empty($appointment)) {
                    $appointment->update($request->all());
                    return $this->response->success(['message' => "Appointment updated succesfully"]);
                }
                return $this->response->fail("Appointment updated failed");
            } catch (\Illuminate\Database\QueryException  $exception) {
                return $this->response->fail(['message' => "response.Database Error"]);
            }

    }


    public function getRequest($url)
    {
        $response = $this->curlServiceProvider->get($this->postCodeApi.$url);
        return $response;
    }

    public function extractAddress($url)
    {
        $response = json_decode($this->getRequest($url));
        if ($response->status !== 200) {
            return 0;
        }
        $response = $response->result;
        $longAdress = $response->country . ',' . $response->admin_district . ',' . $response->admin_county . ',' . $response->admin_ward;
        return $longAdress;
    }


    public function calcDistanceAndDuration($origin, $destination, $time)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $origin . "&destinations=" . $destination . "&sensor=false&key=" . $this->googleMapApi;
        $response = json_decode(file_get_contents($url));

        list($hours, $minutes) = explode(':', $time, 2);
        $seconds = $minutes * 60 + $hours * 3600;

        $distance = $response->rows[0]->elements[0]->distance->text;
        $duration = $response->rows[0]->elements[0]->duration->value;

        $checkSeconds = $seconds - $duration;
        $returnSeconds = $seconds + 3600 + $duration;

        $response = [
            "distance" => $distance,
            "duration" => $duration,
            "time" => gmdate("H:i", $seconds),
            "checkoutTime" => gmdate("H:i", $checkSeconds),
            "returnTime" => gmdate("H:i", $returnSeconds),
        ];
        return $response;
    }
}
