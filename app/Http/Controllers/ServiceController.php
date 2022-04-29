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

    public function datainsert(){
        $services = Service::query()->create([
            'name' => 'Gate 1',
            'street' => 'WoodWard',
            'IsActive' => true,
        ]);
        $services = Service::query()->create([
            'name' => 'Gate 2',
            'street' => 'WoodWard',
            'IsActive' => true,
        ]);
        $services = Service::query()->create([
            'name' => 'Gate 3',
            'street' => 'Farmer',
            'IsActive' => true,
        ]);
        $services = Service::query()->create([
            'name' => 'Gate 4',
            'street' => 'Farmer',
            'IsActive' => true,
        ]);
        $services = Service::query()->create([
            'name' => 'Forklift 1',
            'street' => 'BothStreet',
            'IsActive' => true,
        ]);
        $services = Service::query()->create([
            'name' => 'Forklift 2',
            'street' => 'BothStreet',
            'IsActive' => true,
        ]);
        $services = Service::query()->create([
            'name' => 'BLK BuckHoist',
            'street' => 'BothStreet',
            'IsActive' => false,
        ]);
        $services = Service::query()->create([
            'name' => 'TWR BuckHoist',
            'street' => 'BothStreet',
            'IsActive' => true,
        ]);
        $services = Service::query()->create([
            'name' => 'Tower Crane 1',
            'street' => 'BothStreet',
            'IsActive' => true,
        ]); $services = Service::query()->create([
            'name' => 'Tower Crane 2',
            'street' => 'BothStreet',
            'IsActive' => true,
        ]); $services = Service::query()->create([
            'name' => 'Trailer Pump',
            'street' => 'BothStreet',
            'IsActive' => true,
        ]);
        $services = Service::query()->create([
            'name' => 'Ramp-BSE',
            'street' => 'BothStreet',
            'IsActive' => true,
        ]);
        return \response("done");

    }




    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */

    public function index(): JsonResponse //this action for user
    {
        // delete expired reservations
        $now = Carbon::now();

        Reservation::query()->where('end_time', '<', $now)->delete();

//        StaffReservation::query()->where('end_time', '<', $now)
//            ->where('date', '<', $now)->delete();

        //note: here notice that groupBy method before get method acts as a Query Builder method while after get method acts as a Collection method
        $services["services"]= Service::query()->where('IsActive', '=', true)->get()
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
            'IsActive' => true,
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

    public function deleteService(Service $service): JsonResponse  //this for admin
    {
       // $service->delete();

        return response()->json('product deleted successfully', Response::HTTP_OK);


    }

}
