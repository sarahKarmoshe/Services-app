<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
       $staff= Staff::query()->get();
       return response()->json($staff,Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaffRequest $request
     * @return JsonResponse
     */
    public function store(StoreStaffRequest $request): JsonResponse
    {
        $staff=Staff::query()->create([
            'staff_name'=>$request->name,
        ]);
        return response()->json($staff,Response::HTTP_CREATED);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param Staff $staff
     * @return JsonResponse
     */
    public function destroy(Staff $staff): JsonResponse
    {
        $staff->delete();
        return response()->json('Staff Deleted Successfully ',Response::HTTP_OK);
    }
}
