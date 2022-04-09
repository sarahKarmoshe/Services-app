<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Service;
use App\Http\Requests\StoreServiceRequest;
use App\Models\StaffReservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class ServiceController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */

    public function index(): JsonResponse //this action for user
    {
        // delete expired reservations
        $now = Carbon::now();

        Reservation::query()->where('end_time', '<', $now)
            ->where('date', '<', $now)->delete();

        StaffReservation::query()->where('end_time', '<', $now)
            ->where('date', '<', $now)->delete();

        //note: here notice that groupBy method before get method acts as a Query Builder method while after get method acts as a Collection method
        $services = Service::query()->where('IsActive', '=', true)->get()
            ->groupBy('street');

        return response()->json($services, Response::HTTP_OK);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreServiceRequest $request
     * @return JsonResponse
     */
    public function store(StoreServiceRequest $request): JsonResponse // this for admin
    {
        $services = Service::query()->create([
            'name' => $request->name,
            'street' => $request->street,
            'IsActive' => $request->IsActive,
        ]);
        return response()->json($services, Response::HTTP_CREATED);
    }


    /**
     * Block/Activate the specified resource from storage.
     *
     * @param Service $service
     * @return JsonResponse
     */

    public function BlockService(Service $service): JsonResponse //this for admin
    {
        if ($service->IsActive) {
            $service->update(['IsActive' => false]);
            $state = 'Service has Blocked successfully! ';
        } else {
            $service->update(['IsActive' => true]);
            $state = 'Service has Activated successfully! ';
        }

        return response()->json($state, Response::HTTP_OK);
    }

}
