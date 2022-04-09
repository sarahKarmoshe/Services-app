<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Reservation;
use App\Models\StaffReservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function signUp(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users')],
            'password' => ['required', 'min:8'],
            'c_password' => ['required', 'same:password'],
            'phone' => ['required', 'string'],

        ]);

        if ($validator->fails()) {
            return Response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $request['password'] = Hash::make($request['password']);


        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,

        ]);


        $tokenResult = $user->createToken('personal Access Token')->accessToken;
        $data["user"] = $user;
        $data["tokenType"] = 'Bearer';
        $data["access_token"] = $tokenResult;

        return response()->json($data, Response::HTTP_CREATED);

    }

    /**
     * @throws AuthenticationException
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'email' => ['required', 'email'],
                'password' => ['required', 'min:8'],
            ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            throw new AuthenticationException();
        }

        $user = $request->user();
        $tokenResult = $user->createToken('personal Access Token')->accessToken;
        $data['user'] = $user;
        $data["TokenType"] = 'Bearer';
        $data['Token'] = $tokenResult;

        return response()->json($data, Response::HTTP_OK);
    }

    public function logOut()
    {
        Auth::user()->token()->revoke();

        return response()->json("logged out", Response::HTTP_OK);

    }

    public function ResetPassword(Request $request): \Illuminate\Http\JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'min:8'],
            'c_password' => ['required', 'min:8', 'same:password'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $request['password'] = Hash::make($request['password']);

        Auth::user()->update([
            'password' => $request->password,
        ]);
        return response()->json("password reset has done successfully !", Response::HTTP_OK);
    }


    public function MyReservation() //this for user
    {
        $now = Carbon::now();
        Reservation::query()->where('end_time', '<', $now)
            ->where('date', '<', $now)->delete();

        StaffReservation::query()->where('end_time', '<', $now)
            ->where('date', '<', $now)->delete();


        //get reservation and classificate it by accepted or pending
        $reservation = Reservation::query()->where('user_id', '=', Auth::id())
            ->get()->groupBy('IsAccepted');

        return response()->json($reservation, Response::HTTP_OK);

    }


}
