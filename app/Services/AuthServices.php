<?php

namespace App\Services;

use App\Models\ResetCodePassword;
use App\Models\User;
use Carbon\Carbon;

class AuthServices
{
    public static function checkCode($code, $email)
    {
        $res = ResetCodePassword::where('email', $email)->get()->first();

        if ($res->updated_at > now()->addHour()) {
            return ["success" => false, "message" => "Código Expirado"];
        }
        if (!$res) {
            return ["success" => false, "message" => "Email Inválido"];
        }

        if (sha1($code) == $res->code) {
            return ["success" => true, "message" => "Codigo verificado"];
        } else {
            return ["success" => false, "message" => "Codigo não verificado"];
        }
    }

    public static function checkCodeByCode($code)
    {
        $currentCodeData = ResetCodePassword::where('code', $code)->get()->first();
        if ($currentCodeData) {
            return ["success" => true, "message" => "Email verificado", "email" => $currentCodeData->email];
        }
        return ["success" => false, "message" => "Email não verificado 1",];
    }
}
