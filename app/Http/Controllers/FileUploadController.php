<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileUploadController extends Controller
{
  
    public  function dropzoneUi()
    {
        return view('upload-view');
    }
  
    public  function dropzoneFileUpload(Request $request)
    {
        $image_folder="images/";
        $image = $request->file('file');
        $imageName = time().'.'.$image->extension();
        $image->move(public_path('images'),$imageName);
        $image_toconvert = imagecreatefromstring(file_get_contents($image_folder . $imageName));
        imagewebp($image_toconvert , $image_folder.$imageName.'.webp', 80);
        unlink($image_folder.$imageName);

        return response()->json(['success'=>$imageName.".webp"]);
    }
}