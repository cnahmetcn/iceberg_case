<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    } //end __construct()


    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'    => 'required|email',
                'password' => 'required|string|min:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $token_validity = (24 * 60);

        $this->guard()->factory()->setTTL($token_validity);

        if (!$token = $this->guard()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    } //end login()


    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'fullName'     => 'required|string|between:2,100',
                'email'    => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
                'phone' => 'max:15',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [$validator->errors()],
                422
            );
        }

        $user = User::create(
            array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            )
        );

        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }

    public function update (User $userId, Request $request, $id)
    {
        $loggedUser = Auth::guard('api')->id();
        $userId = User::findOrFail($id);

        if ($userId->id == $loggedUser) {
            $email = $request->email;
            $fullName = $request->fullName;
            $password = $request->password;
            $phone = $request->phone;

            $user = User::find($id);
            $user->email = $email;
            $user->fullName = $fullName;
            $user->phone = $phone;
            $user->password = bcrypt($password);
            $user->save();

            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        }
        else if($userId->id != $loggedUser)
        {
            return response()->json(['message' => 'You are not authorized to update this user'], 401);
        }



    }


    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'User logged out successfully']);
    } //end logout()


    public function me()
    {
        return response()->json($this->guard()->user());
    } //end profile()


    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    } //end refresh()


    protected function respondWithToken($token)
    {
        return response()->json(
            [
                'token'          => $token,
                'token_type'     => 'bearer',
                'token_validity' => ($this->guard()->factory()->getTTL() * 60),
            ]
        );
    } //end respondWithToken()


    protected function guard()
    {
        return Auth::guard();
    } //end guard()


}//end class

