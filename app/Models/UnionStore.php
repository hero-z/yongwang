<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UnionStore extends Model
{


	protected $table='union_store';
	protected $fillable=[
'pid',
'store_id',
'store_name',
'merchant_id',
'app_id',
'app_key',
'mobile',
'shop_user',
'status',
'province',
'city',
'district',
'preaddress',
'endaddress',
'user_id',
	];

	// 生成商户号
	public static function makeStoreId()
	{
		return 'u_'.date('YmdHis').rand(100000,999999);
	}
}