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

    public function DeleteUnVerifiedAccounts(){
    $now=Carbon::now();
    $sub=$now->addHour(-24);
    User::query()->where('email_verified_at','=','Null')
        ->where('created_at','<',$sub)->delete();

    }

    public function signUp(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->DeleteUnVerifiedAccounts();

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

        $verification_code = substr(number_format(rand(), 0, '', ''), 0, 6);
        $user->sendApiEmailVerificationNotification($verification_code);


        $tokenResult = $user->createToken('personal Access Token')->accessToken;
        $data["user"] = $user;
        $data["verification_code"] = $verification_code;
        $data['IsAdmin']=false;
        $data["tokenType"] = 'Bearer';
        $data["access_token"] = $tokenResult;

        return response()->json($data, Response::HTTP_CREATED);

    }

    public function verify(Request $request) {

        $userID = $request->id;
        $user = User::findOrFail($userID);
        $date = date("Y-m-d g:i:s");
        $user->email_verified_at = $date;
        $user->save();

        return response()->json("Email verified!" ,Response::HTTP_OK);
    }

    public function resend(Request $request){
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json("User already have verified email!", 422);
        }

        $verification_code = substr(number_format(  rand(), 0, '', ''), 0, 6);
        $request->user()->sendEmailVerificationNotification($verification_code);

        return response()->json("The notification has been resubmitted");

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
        $data['IsAdmin']=false;
        $data["TokenType"] = 'Bearer';
        $data['Token'] = $tokenResult;

        return response()->json($data, Response::HTTP_OK);
    }

    public function logOut()
    {
        Auth::user()->token()->revoke();
        return response()->json("logged out", Response::HTTP_OK);

    }

    public function ResetPasswordRequest(){
        $user=Auth::user();
        $verification_code = substr(number_format(rand(), 0, '', ''), 0, 6);
        $user->sendEmailVerificationPassword($verification_code);

        $response['reset password code']=$verification_code;
        return response()->json($response,Response::HTTP_OK);

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
