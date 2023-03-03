<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error.',
            ]);
        }

        $validated = $validator->validated();

        $user = User::where('email',$validated['email'])->first();

        $hash = Hash::check($validated['password'], $user->password);

        if($user && $hash){
            $token = $user->createToken('user-token')->plainTextToken;            

            return response()->json([
                '_token' => $token,
                'status' => 200
            ]);
        }else{
            return response()->json([
                'message' => 'Wrong Credentials.',
                'status' => 200
            ]);
        }
    }
}
