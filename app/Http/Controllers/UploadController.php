<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageStoreRequest;
use App\Http\Responses\ResponseApi;
use App\Models\Image;
use App\Models\Provider;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class UploadController extends Controller
{
    public function imageStore(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
                'type' => 'required'
            ]);

            $savedUrl = "";
            $userId = auth()->user()->id;
            $imageName = time() . '.' . $request->image->extension();

            // Public Folder
            $request->image->move(public_path("user-" . $userId . "/images"), $imageName);

            $url = url("/user-" . $userId . "/images/" . $imageName);
            if($request->type === 'avatar'){
           

            $userProvider = Provider::where('user_id', $userId)->get()->first();

            if ($userProvider) {
                $userProvider->avatar = $url;
            } else {
                $userProvider = new provider();
                $userProvider->avatar = $url;
                $userProvider->provider = "email";
                $userProvider->provider_id = "nailminder.email";
                $userProvider->user_id = $userId;
            }
            $userProvider->save();
            $savedUrl = $userProvider->avatar;
        }


        if($request->type === 'logo'){
            $userImage = Image::where('user_id', $userId)->get()->first();

            if ($userImage) {
                $userImage->image = $url;
            } else {
                $userImage = new Image();
                $userImage->image = $url;
                $userImage->user_id = $userId;
            }
            $userImage->save();
            $savedUrl = $userImage->image;
        }

            return ResponseApi::ok(["url" => $savedUrl]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
}
