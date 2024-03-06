<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return "hello";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $product = new Product();
        $product->product_name = $request->name;
        $product->save();
        return response()->json([
            'product' => $product,
            'message' => 'product stored',
            'status' => 1
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $product = Product::where('id', $id)->first();
        if(is_null($product)){
            return response()->json([
                'product' => null,
                'message' => 'product not found',
                'status' => 0
            ]);
        }else{
            return response()->json([
                'product' => $product,
                'message' => 'product found',
                'status' => 1
            ]);    
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $product = Product::where('id',$id)->first();
        if(is_null($product)){
            return response()->json([
                'message' => 'data not found',
                'status' => '404'
            ]);
        }else{
            $product->product_name = $request->name;
            if($product->save()){
                return response()->json([
                'message' => 'Data inserted successfully',
                'status' => '200'
            ]);
            }else{
                return response()->json([
                'message' => 'something went worng',
                'status' => '404'
            ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $product = Product::where('id',$id)->delete();
        if(is_null($product)){
            return response()->json([
                'product' => null,
                'message' => 'product not delete',
                'status' => 0
            ]);
        }else{
            return response()->json([
                'product' => $product,
                'message' => 'product deleted',
                'status' => 1
            ]);
        }
    }
    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed'
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'user registered',
            'status' => 1
        ]);
    }
    public function login(Request $request){
        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        $user = User::where('email', $validatedData['email'])->first();
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password',
                'status' => 0
            ], 401); // Unauthorized
        }
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Logged in',
            'status' => 1
        ]);
    }

    public function getUser($id){
        $user = User::where('id', $id)->first();
        if(is_null($user)){
            return response()->json([
                'user' => null,
                'message' => 'user not found',
                'status' => 0
            ]);
        }else{
            return response()->json([
                'user' => $user,
                'message' => 'user found',
                'status' => 1
            ]);    
        }
    }
}
