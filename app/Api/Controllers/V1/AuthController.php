<?php

namespace App\Api\Controllers\V1;

use Illuminate\Support\Facades\Validator;
use App\Core\Status;
use Illuminate\Http\Exception\HttpResponseException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Api\Controllers\BaseController;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;

class AuthController extends BaseController
{
    /**
     * @api {post} /auth/login 登录
     */
    public function login(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('username', 'password');
        //return $this->successResponse($credentials);
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse(Status::RET_ACCOUNT_WRONG);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->errorResponse(Status::RET_COUNT_NOT_CREATE);
        }
        // all good so return the token
        return response()->json(compact('token'));
    }

    /**
     * @api {post} /auth/refreshToken jwt刷新token
     */
    public function refreshToken()
    {
        $newToken = JWTAuth::parseToken()->refresh();
        return $this->successResponse(['token' => $newToken]);
    }

    /**
     * @api {post} /auth/signup 注册
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'  => 'required|max:255|min:2',
            'email'    => 'required|email|unique:users',
            'password'     => 'required|min:6',
        ],[
            'email.unique' => trans('demo.email_has_registed'),
            'password.min' => '密码最少为6位字符'
        ]);

        if ($validator->fails()) {
            return $this->response->error($validator->messages(), 403);
        }
        //return $this->successResponse($request->only('username', 'email', 'password'));
//        $data = [
//            'username' => $request->get('username'),
//            'email' => $request->get('email'),
//            'password' => bcrypt($request->get('username')),
//        ];
//        $user = User::create($data);

        $user = new User;
        $user->email = $request->get('email');
        $user->username = $request->get('username');
        $user->password = bcrypt($request->get('password'));
        $user->group = $request->get('group');
        $user->save();

        // 用户注册事件
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('token'));
    }
}