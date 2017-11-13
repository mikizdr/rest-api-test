<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function signup (Request $request)
    {
    	$this->validate($request, [
    		'name' => 'required',
    		'email' => 'required|email|unique:users',
    		'password' => 'required|min:8'
    	]);

    	$user = new User([
    		'name' => $request->input('name'),
    		'email' => $request->input('email'),
    		'password' => bcrypt($request->input('password')),
    	]);

    	$user->save();

    	return response()->json([
    		'message' => 'Successfully created user!'
    	], 201);
    }

	public function signin (Request $request)
	{
		// validate data
    	$this->validate($request, [
    		'name' => 'required',
    		'email' => 'required|email',
    		'password' => 'required'
    	]);

    	// get only data that are needed for authentication
    	$credentials = $request->only('email', 'password');

    	// trying to see if email and password are correct
    	try {
    		if (!$token = JWTAuth::attempt($credentials)) {
    			return response()->json([
    				'error' => 'Invalid credentials!'
    			], 401);
    		}
    	} catch (JWTException $e) {
    		return response()->json([
    			'error' => 'Couldn\'t create token.'
    		], 500);
    	}

    	// everything is ok, then send a valid token
    	return response()->json([
    		'token' => $token
    	], 200);

	}

}
