<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseApi;
use App\Models\UserLayoutPage;
use Illuminate\Http\Request;

class UserLayoutPageController extends Controller
{
    public function getLayouts()
    {


        try {

            $data = UserLayoutPage::all();

            return ResponseApi::ok(["data" => $data]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
    public function getLayoutById($layout_id)
    {


        try {

            $data = UserLayoutPage::find($layout_id);


            return ResponseApi::ok(["data" => $data]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
    public function updateLayout($layout_id, Request $request)
    {
        try {

            $data = UserLayoutPage::find($layout_id);
            $data->page_id = $request->page_id;
            $data->save();



            return ResponseApi::ok(["data" => $data, "request" => $request->page_id]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
}
