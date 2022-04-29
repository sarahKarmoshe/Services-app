<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function signUp(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('admins')],
            'password' => ['required', 'min:8'],
            'c_password' => ['required', 'same:password'],
            'phone' => ['required', 'string'],

        ]);

        if ($validator->fails()) {
            return Response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $request['password'] = Hash::make($request['password']);


        $admin = Admin::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,

        ]);

        $tokenResult = $admin->createToken('personal Access Token')->accessToken;
        $data["user"] = $admin;
        $data['IsAdmin']=true;
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


        if(auth()->guard('admin')->attempt(['email' => request('email'), 'password' => request('password')])) {

            config(['auth.guards.api.provider' => 'admin']);

            $admin = Admin::find(auth()->guard('admin')->user()->id);

            $tokenResult = $admin->createToken('personal Access Token')->accessToken;
            $data['admin'] = $admin;
            $data['IsAdmin']=true;
            $data["TokenType"] = 'Bearer';
            $data['Token'] = $tokenResult;
        }
        else{
            throw new AuthenticationException();

        }

        return response()->json($data, Response::HTTP_OK);
    }

    public function logOut()
    {
        Auth::user()->token()->revoke();

        return response()->json("logged out", Response::HTTP_OK);

    }

    public function ResetPasswordRequest(){
        config(['auth.guards.api.provider' => 'admin']);

        $admin = Admin::find(Auth::guard('admin-api')->id());

        $verification_code = substr(number_format(rand(), 0, '', ''), 0, 6);
        $admin->sendEmailVerificationPasswordAdmin($verification_code);

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
        $admin= Admin::find(Auth::guard('admin-api')->id())->get();
        $admin->update([
            'password' => $request->password,
        ]);
        return response()->json("password reset has done successfully !", Response::HTTP_OK);
    }

    public function ProfileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return Response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        Auth::user()->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
        ]);
        $admin= Admin::find(Auth::guard('admin-api')->id())->get();

        return response()->json($admin,Response::HTTP_OK);
    }
}
