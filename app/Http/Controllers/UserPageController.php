<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseApi;
use App\Models\DashItems;
use App\Models\UserLayoutPage;
use App\Models\UserPage;
use Illuminate\Http\Request;

class UserPageController extends Controller
{
    public function register(Request $request)
    {


        try {
            $request->validate([
                'title' => 'required',
                'layout_id' => 'required'
            ]);

            $data = $request->all();
            $page = UserPage::create($data);

            return ResponseApi::create(["page" => $page]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
    public function getPageById($user_id)
    {
        try {
            $page = UserPage::where([
                ['user_id', '=', $user_id],
                ['active', '=', 'Y']
            ])->first();

            if (!$page) {
                return ResponseApi::error([], 'Página não encontrada.');
            }

            $layout = $page->layout()->first();

            if (!$layout) {
                return ResponseApi::error([], 'Layout não encontrado.');
            }

            $items = $layout->items()->get();

            $new_items = [];

            foreach ($items as $item) {
                $itemData = $item->toArray();
                $itemData['children'] = [];

                if ($item->content) {
                    // Divide a string para obter a tabela e a coluna
                    list($table, $column) = explode('$', $item->content);

                    // Obtém o valor da coluna correspondente na tabela
                    $columnValue = \DB::table($table)->where('id', $page->id)->value($column);

                    // Substitui $item->content pelo valor da coluna
                    $itemData['content'] = $columnValue;
                }

                if ($item->parent_id === null) {
                    $new_items[$item->id] = $itemData;
                } else {
                    $new_items[$item->parent_id]['children'][] = $itemData;
                }
            }


            $tree = array_values($new_items);

            $layoutData = $layout->toArray();
            $layoutData['items'] = $tree;

            $pageData = $page->toArray();
            $pageData['layout'] = $layoutData;

            return ResponseApi::ok(["page" => $pageData]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
    public function getPageByUserId($user_id)
    {
        try {
            $page = UserPage::where('user_id', $user_id)->first();
            return ResponseApi::ok(["page" => $page]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
    public function updatePage($page_id, Request $request){
        try {
            $page = UserPage::find($page_id);
            if($request->description){
                $page->description = $request->description;
            }
            if($request->title){
                $page->title = $request->title;
            }
            $page->save();
            return ResponseApi::ok(["page" => $page]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
}
