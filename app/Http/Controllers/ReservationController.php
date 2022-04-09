<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\StaffReservation;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse   //this for admin
    {
        // delete expired reservations
        $now = Carbon::now();
        Reservation::query()->where('end_time', '<', $now)
                            ->where('date','<',$now)->delete();

        StaffReservation::query()->where('end_time', '<', $now)
            ->where('date','<',$now)->delete();

        $reservation = Reservation::query()->get();
        return response()->json($reservation, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreReservationRequest $request
     * @return JsonResponse
     */


    public function store(StoreReservationRequest $request): JsonResponse //this for user
    {

        $start_time = Carbon::parse($request->start_time);
        $end_time = $start_time->addMinute($request->period);
        $end_time->setDate($request->year, $request->month, $request->day);

        $reservation = Reservation::query()->create([
            'user_id' => Auth::id(),
            'service_id' => $request->service_id,
            'start_time' => $request->start_time,
            'end_time' => $end_time,
            'date'=>$end_time,
            'Gate_name' => $request->Gate_name,
        ]);


 // reserve the Gate with service
        $service=Service::query()->where('name','=',$request->Gate_name)->get();
        Reservation::query()->create([
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'start_time' => $request->start_time,
            'end_time' => $end_time,
            'date'=>$end_time,
            'Gate_name' => $request->Gate_name,
        ]);



//staff reservation
        foreach ($request->staffs_id as $item) {

            StaffReservation::query()->create([
                'staff_id' => $item,
                'service_id' => $request->service_id,
                'start_time' => $request->start_time,
                'date'=>$end_time,
                'end_time' => $end_time,
            ]);

        }
        //detect if service is a street or not
        $service = Service::query()->find($request->service_id);
        if ($service->name == 'WoodWard') {
            for ($i = 1; $i <= 2; $i++) {
                Reservation::query()->create([
                    'user_id' => Auth::id(),
                    'service_id' => $i, //Gate
                    'start_time' => $request->start_time,
                    'end_time' => $end_time,
                    'date'=>$end_time,
                    'Gate_name' => $request->Gate_name,
                ]);
            }
        }
        if ($service->name == 'Farmer') {
            for ($i = 3; $i <= 4; $i++) {
                Reservation::query()->create([
                    'user_id' => Auth::id(),
                    'service_id' => $i, //Gate
                    'start_time' => $request->start_time,
                    'end_time' => $end_time,
                    'date'=>$end_time,
                    'Gate_name' => $request->Gate_name,
                ]);
            }
        }


        return response()->json($reservation, Response::HTTP_CREATED);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateReservationRequest $request
     * @param Reservation $reservation
     * @return JsonResponse
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation): JsonResponse //this for user
    {
        $start_time = Carbon::parse($request->start_time);
        $end_time = $start_time->addMinute($request->period);
        $end_time->setDate($request->year, $request->month, $request->day);

        $reservation->query()->update([
            'user_id' => Auth::id(),
            'service_id' => $request->service_id,
            'start_time' => $request->start_time,
            'end_time' => $end_time,
            'date'=>$end_time,
            'Gate_name' => $request->Gate_name,
        ]);
        foreach ($request->staffs_id as $item)
        {
            StaffReservation::query()->create([
                'staff_id' => $item,
                'service_id' => $request->service_id,
                'start_time' => $request->start_time,
                'date'=>$end_time,
                'end_time' => $end_time,
            ]);
        }
        //detect if service is a street or not
        $service = Service::query()->find($request->service_id);
        if ($service->name == 'WoodWard') {
            for ($i = 1; $i <= 2; $i++) {
                Reservation::query()->create([
                    'user_id' => Auth::id(),
                    'service_id' => $i, //Gate
                    'start_time' => $request->start_time,
                    'end_time' => $end_time,
                    'date'=>$end_time,
                    'Gate_name' => $request->Gate_name,
                ]);
            }
        }
        if ($service->name == 'Farmer') {
            for ($i = 3; $i <= 4; $i++) {
                Reservation::query()->create([
                    'user_id' => Auth::id(),
                    'service_id' => $i, //Gate
                    'start_time' => $request->start_time,
                    'end_time' => $end_time,
                    'date'=>$end_time,
                    'Gate_name' => $request->Gate_name,
                ]);
            }
        }


        return response()->json($reservation, Response::HTTP_OK);

    }

    /**
     * Remove the specified resource from storage.
     * @param Reservation $reservation
     * @return JsonResponse
     * @throws  UnauthorizedException
     */
    public function destroy(Reservation $reservation): JsonResponse //this for user
    {
        if ($reservation->user_id != Auth::id()) {
            return response()->json('UnAuthorized To Do This Action !', Response::HTTP_UNAUTHORIZED);
        }

        $reservation->delete();
        return response()->json('reservation deleted successfully', Response::HTTP_OK);

    }

    public function DestroyByAdmin(Reservation $reservation): JsonResponse //this for Admin
    {
        $reservation->delete();
        return response()->json('reservation deleted successfully', Response::HTTP_OK);

    }

    public function AcceptReservation(Reservation $reservation): JsonResponse //this for Admin
    {

        $reservation->update(['IsAccepted' => true]);
        $state = 'Reservation has Accepted successfully! ';

        return response()->json($state, Response::HTTP_OK);

    }
}
