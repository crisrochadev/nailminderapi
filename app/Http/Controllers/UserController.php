<?php

namespace App\Http\Controllers;

use App\Http\Responses\ResponseApi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $user->avatar = $user->providers()->pluck('avatar')->first();
            $user->logo = $user->images()->pluck('image')->first();

            return ResponseApi::create([
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
    public function update(Request $request)
    {
        try {
            $user = $request->user();

            $data = $request->all(); // Atualiza os dados do usuário

            $user->fill($data); // Preenche apenas os campos existentes no modelo

            $user->save(); // Salva as alterações no banco de dados

            return ResponseApi::create($user);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
    public function updatePass(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
            'email' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        return ResponseApi::ok([], "Senha alterada com sucesso!");
    }
    public function getUserBySlug($slug)
    {
        try {

            $user = User::where('slug',$slug)->first();

            if (!$user) {
                return ResponseApi::error([], 'Página não encontrada.');
            }
            return ResponseApi::ok([
                'id' => $user->id
            ]);
        } catch (\Exception $e) {
            return ResponseApi::error([], $e->getMessage());
        }
    }
}
