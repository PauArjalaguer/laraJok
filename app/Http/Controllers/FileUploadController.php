<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    /** 
     * Generate Upload View 
     * 
     * @return void 

    */  

    public  function dropzoneUi()  
    {  
        return view('upload-view');  
    }  

    /** 
     * File Upload Method 
     * 
     * @return void 
     */  

    public  function dropzoneFileUpload(Request $request)  
    {  
        $image = $request->file('file');

     
        $imageName = time().'.'.$image->extension(); 

       // 

 /*       if (extension_loaded('gd') && function_exists('gd_info')) {
        echo "GD is installed.";
    } else {
        echo "GD is not installed.";
    } */
        $image->move(public_path('images'),$imageName);  
        $image_toconvert = imagecreatefromstring(file_get_contents('images/' . $imageName));
        imagewebp($image_toconvert , "images/".$imageName.'.webp', 80);
        unlink("images/".$imageName);

        return response()->json(['success'=>$imageName.".webp"]);
    }
}