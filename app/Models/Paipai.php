<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paipai extends Model
{
    //

	protected $table ='paipai';
	protected $fillable = [

'store_id',
'm_id',
'device_no',
'device_pwd',	
'name',
'status'
    ];




}
