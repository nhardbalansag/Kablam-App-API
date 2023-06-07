<?php

namespace App\Models\V1\Roles\Query;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RolesQueryBuilder extends Model
{
    use HasFactory;

    public static function getUserInformation(){

        $data = DB::table('users')
                ->where('id', Auth::user()->id)
                ->first();

        return $data;
    }
}
