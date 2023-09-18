<?php

namespace App\Http\Controllers;

use App\Models\UserPage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class UserPageController extends Controller
{
    public function register(Request $request)
    {

        try {
          $data = $request->all();
          UserPage::insert
        } catch (\Throwable $th) {
            return Response::json(["error" => true]);
        }
       
    }

}
