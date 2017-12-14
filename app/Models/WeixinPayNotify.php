<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeixinPayNotify extends Model
{
    //

    protected  $fillable=['store_type','store_id','store_name','template_id','receiver','topColor','linkTo','data'];
}
