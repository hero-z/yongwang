<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JpushConfig extends Model
{
     protected  $fillable=['DevKey','API_DevSecret','created_at',"updated_at"];
}
