<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class UsersController extends Controller {
    // *
    //  * Create a new controller instance.
    //  *
    //  * @return void


    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function authenticate(Request $request) {

        Log::info("hiiidsfoiasjdfisa");
        $this->validate($request, [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        $user = Users::where('email', $request->input('email'))->first();

        Log::info($user->password);
        Log::info($user->email);

        if (Hash::check($request->input('password'), $user->password)) {
            $apikey = base64_encode(str_random(40));
            Users::where('email', $request->input('email'))->update(['api_key' => "$apikey"]);;

            return response()->json(['status' => 'success','api_key' => $apikey]);

        } else {
            return response()->json(['status' => 'fail'],401);

        }

    }

    //
}
