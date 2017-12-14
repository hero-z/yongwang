<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    //
    protected $fillable=['app_id','token','app_version','msg'];
}
