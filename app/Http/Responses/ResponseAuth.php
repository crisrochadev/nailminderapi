<?php

namespace App\Http\Responses;


class ResponseAuth {
    public static function ok($data = [],$message = null){
        if($message === null){
            $message = "Requisição aceita com sucesso";
        }
        return response()->json([
            "success" => true,
            "data" => $data,
            "message" => $message
        ],200);
    }
    public static function create($data = [],$message = null){
        if($message === null){
            $message = "Dados criados ou atualizados com sucesso";
        }
        return response()->json([
            "success" => true,
            "data" => $data,
            "message" => $message
        ],202);
    }
    public static function notFound($data = [],$message = null){
        if($message === null){
            $message = "A solicitação não foi encontrada";
        }
        return response()->json([
            "success" => false,
            "data" => $data,
            "message" => $message
        ],404);
    }
    public static function error($data = [],$message = null){
        if($message === null){
            $message = "Houve um erro na requisição";
        }
        return response()->json([
            "success" => false,
            "data" => $data,
            "message" => $message
        ],422);
    }
}