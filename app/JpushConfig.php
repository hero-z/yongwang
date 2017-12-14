<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JpushConfig extends Model
{
     protected  $fillable=['DevKey','API_DevSecret','created_at',"updated_at"];
}
