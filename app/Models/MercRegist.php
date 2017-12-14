<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MercRegist extends Model
{
    //
    protected  $fillable=['user_id',"store_name",'store_id','stl_sign','org_no','stl_oac','bnk_acnm','icrp_id_no',"crp_exp_dt_tmp","wc_lbnk_no",
        "bse_lice_nm","crp_nm","merc_adds","bse_lice_limited","corporate_idcord","corporate_idlimited","stoe_nm","toe_cnt_nm","stoe_cnt_tel","mcc_cd","stoe_area_cod","stoe_adds","fee_rat","max_fee_amt","fee_rat1","fee_rat_scan","fee_rat1_scan","brown_bli","mer_res_img","merc_ogcc_img","crp_cs_img","crp_os_img","door_img","foy_img","choc_img","merc_bankcode_img","hold_img","met_img","merc_openbank_img","check_flag","merc_id",
        "stoe_id","corg_merc_id","corg_merc_id_scan","trm_no","key","pid"
    ];

}
