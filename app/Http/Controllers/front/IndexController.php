<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Img;

class IndexController extends Controller
{
    public function index(){
        $users = User::get();
        return view('front.index',compact('users'));
    }
    public function getdata($id){
        $user = User::where('id', $id)->with(['userimg'])->first();
        // return response()->json([
        //     'username' => $user->name,
        //     'useremail' => $user->email,
        //     'id' => $user->id
        // ]);
        $responseData = [
            'username' => $user->name,
            'useremail' => $user->email,
            'id' => $user->id,
            'images' => [],
            'imageid' => []
        ];

        foreach ($user->userimg as $image) {
            $responseData['images'][] = $image->url; // Assuming 'url' is the column where the image URL is stored
            $responseData['imageid'][] = $image->id;
        }

        return response()->json($responseData);
    }
    public function update(Request $request){
        $id = $request->input('id');
        $user = User::where('id', $id)->first();
        if ($user) {
            $user->name = $request->input('username');
            $user->email = $request->input('userEmail');
            $user->save();

            if (isset($request->image)) {
                if ($file = $request->file('image')) {
                    $field_values_array = $request->image;
                    foreach($request->image as $key => $value){
                        $detailPageImgFileName = 'user_img_' .$key .'_' . time() . '.' . $value ->getClientOriginalExtension();
                        $savePath = public_path('/images');
                        $value->move($savePath, $detailPageImgFileName);
                        $pname =  'images/' . $detailPageImgFileName;
                        Img::create(['url' => $pname,'user_id'=>$user->id]);
                    }
                }
            }
            return redirect('index');
        }else{
            return "error aa gai";
        }
        // echo "<pre>";
        // print_r($request->all());
    }

    public  function FileUpload(Request $request)
    {
        // $image = $request->file('file');
        // $imageName = time().'.'.$image->extension();
        // $image->move(public_path('images'),$imageName);
        // return response()->json(['success'=>$imageName]);
        echo "<pre>";
        print_r($request->all());

        // if (isset($request->image)) {
        //     if ($file = $request->file('image')) {
        //         $field_values_array = $request->image;
        //         foreach($request->image as $key => $value){
        //             $detailPageImgFileName = 'user_img_' .$key .'_' . time() . '.' . $value ->getClientOriginalExtension();
        //             $savePath = public_path('/images');
        //             $value->move($savePath, $detailPageImgFileName);
        //             $pname =  'images/' . $detailPageImgFileName;
        //             Img::create(['url' => $pname,'user_id'=>$user->id]);
        //         }
        //     }
        // }
    }

    public function imagedelete(Request $request){
        $image = Img::where('id',$request->id)->delete();
        // return "hello";

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }
}
