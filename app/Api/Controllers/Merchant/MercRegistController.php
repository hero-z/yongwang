<?php
namespace App\Api\Controllers\Merchant;
use App\Models\MercRegist;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Validator;

class MercRegistController extends BaseController{
    public $url="http://139.196.141.163:8580/mrbui/partpub2/mercRegist.json";
    public $postCharset = "GBK";
    private $fileCharset = "UTF-8";
    public $contentType = "application/x-www-form-urlencoded";

    protected function curl($url,$postFields = null){
    //初始化
        $curl=curl_init();

        //请求地址
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        //设置请求方式,不验证证书
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);//https
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        //设置传输方法
        $data="";
       foreach ($postFields as $k =>$v){
            $data.= "&" . "$k" . "=" . "$v";
       }
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_POST, true);

        //设置字符集
        curl_setopt($curl, CURLOPT_HTTPHEADER,array('Content-Type: application/x-www-form-urlencoded; charset=GBK'));
        //执行
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $reponse = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new \Exception(curl_error($curl), 0);
        } else {
            $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new \Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($curl);
        return $reponse;

    }

    public function mercRegist(Request $request){
            $user = $this->getMerchantInfo();
        try{

            //结算标志,不传默认1对私,0对公
            $data['stl_sign']=$request->get('stl_sign');
            //合作商机构号,6位数字
            $data['org_no']=$request->get('org_no');
            //结算账户,1-23 位数字
            $data['stl_oac']=$request->get('stl_oac');
            //账户,2-45个字符，汉字，字母，数字
            $data['bnk_acnm']=$request->get('bnk_acnm');
            //身份证号,结算标志为1-对私必输，对私：必输，18位数字对公，后台默认给法人身份证号
            $data['icrp_id_no']=$request->get("icrp_id_no");
            //身份证有限期,结算标志为1-对私必输对私：输入格式 1999-12-31对公：默认法人身份证到期日
            $data['crp_exp_dt_tmp']=$request->get('crp_exp_dt_tmp');
            //开户行,联行行号12位数字校验正确性
            $data['wc_lbnk_no']=$request->get('wc_lbnk_no');
            //营业执照号,长度1-27，字母，数字
            $data['bus_lic_no']=$request->get('bus_lic_no');
            //营业执照名,长度1-36，字母，数字
            $data['bse_lice_nm']=$request->get('bse_lice_nm');
            //法人姓名,2-10 汉字、数字、字母不能全为数字
            $data['crp_nm']=$request->get('crp_nm');
            //营业执照地址
            $data['merc_adds']=$request->get('merc_adds');
            //营业执照有限期，格式：1999-12-31，不能小于当前日期
            $data['bse_lice_limited']=$request->get('bse_lice_limited');
            //法人身份证，18位数字
            $data['corporate_idcord']=$request->get("corporate_idcord");
            //法人身份证有限期，格式：1999-12-31，不能小于当前日期
            $data['corporate_idlimited']=$request->get('corporate_idlimited');
            //签购单名称，8-20个数字、字母、汉字不能全为数字
            $data['stoe_nm']=$request->get('stoe_nm');
            //联系人名称,长度2-10，字母、数字、汉字不能全为数字
            $data['stoe_cnt_nm']=$request->get('stoe_cnt_nm');
            //联系人手机号,11位数字
            $data['stoe_cnt_tel']=$request->get("stoe_cnt_tel");
            //商户类型，MCC码，校验合法性
            $data['mcc_cd']=$request->get("mcc_cd");
            //地区码,6位数字，校验合法性
            $data['stoe_area_cod']=$request->get('stoe_area_cod');
           // 商户地址
            $data['stoe_adds']=$request->get('stoe_adds');
            //借记卡费率(%),默认0.5，0.4%-3% 浮动
            $data['fee_rat']=$request->get('fee_rat');
            //借记卡封顶（元）,默认20，最低18， 0表示不封顶，不超过2位数字
            $data['max_fee_amt']=$request->get('max_fee_amt');
            //贷记卡费率（%）,默认0.6，贷记卡0.52%-3%范围浮动
            $data['fee_rat1']=$request->get('fee_rat1');
            //扫码费率(%),默认：0.38，（0.25%-3%）范围浮动，产品类型：扫码支付勾选时必输
            $data['fee_rat_scan']=$request->get('fee_rat_scan');
            //银联二维码费率,默认：0.38
            $data['fee_rat1_scan']=$request->get('fee_rat1_scan');
            //营业执照照片,*营业执照/税务登记证/组织机构代码证/法人证件照(至少提供一项,不超过3张)，以base64加密encode编码传输
            $data['brown_bli']=urlencode(base64_encode($request->get('brown_bli')));
            //税务登记证照,*营业执照/税务登记证/组织机构代码证/法人证件照(至少提供一项,不超过3张)，以base64加密encode编码传输
            $data['mer_res_img']=urlencode(base64_encode($request->get('mer_res_img')));
            //组织机构照
            $data['merc_ogcc_img']=urlencode(base64_encode($request->get('merc_ogcc_img')));
            //法人证件照
            $data['crp_cs_img']=urlencode(base64_encode($request->get('crp_cs_img')));
            //	法人持证件照
            $data['crp_os_img']=urlencode(base64_encode($request->get('crp_os_img')));
            //门头照,*营业场所照(门头、收营台、营业场景)(至少提供一项,以base64加密encode编码传输)
            $data['door_img']=urlencode(base64_encode($request->get('door_img')));
            //场景照,*营业场所照(门头、收营台、营业场景)(至少提供一项,以base64加密encode编码传输)
            $data['foy_img']=urlencode(base64_encode($request->get('foy_img')));
            //收银台照,*营业场所照(门头、收营台、营业场景)(至少提供一项,以base64加密encode编码传输)
            $data['choc_img']=urlencode(base64_encode($request->get('choc_img')));
            //结算人身份证照,*结算信息照(至少提供一项以base64加密encode编码传输)
            $data['merc_bankcode_img']=urlencode(base64_encode($request->get('merc_bankcode_img')));
            //结算人手持证件照
            $data['hold_img']=urlencode(base64_encode($request->get('hold_img')));
            //银行卡照
            $data['met_img']=urlencode(base64_encode($request->get('met_img')));
            //开户行许可证照
            $data['merc_openbank_img']=$data['met_img']=urlencode(base64_encode($request->get('merc_openbank_img')));
            //联系人名称
            $data['stoe_cnt_nm']=$request->get('stoe_cnt_nm');
            $rules = [
                'org_no' => 'required|max:6',
                "stl_oac"=>"required|max:23|min:1",
                "icrp_id_no"=>"required|max:18|min:18",
                "crp_exp_dt_tmp"=>"required|date",
                "wc_lbnk_no"=>"required|max:12|min:12",
                "bus_lic_no"=>"required|max:27|min:1",
                "bse_lice_nm"=>"required|max:36|min:1",
                "bse_lice_limited"=>"required|date",
                "bnk_acnm"=>"required|between:2,45",
                "crp_nm"=>"required|between:2,10",
                "merc_adds"=>"required",
                "corporate_idcord"=>"required|max:18|min:18",
                "corporate_idlimited"=>"required|date",
                "stoe_nm"=>"required|between:8,20",
                "stoe_cnt_nm"=>"required|between:2,10",
                "stoe_cnt_tel"=>"required|max:11|min:11",
                "stoe_area_cod"=>"required|max:6|min:6",
                "mcc_cd"=>"required",
                "stoe_adds"=>"required",
                "max_fee_amt"=>"required",
                "fee_rat1"=>"required",
                "fee_rat_scan"=>"required",
                "fee_rat1_scan"=>"required",

            ];
            $messages = [
                "org_no.required"=>"机构号不能为空",
                "stl_oac.required"=>"结算账户不能为空",
                "bnk_acnm.required"=>"账户不能为空",
                "icrp_id_no.required"=>"身份证号不能为空",
                "crp_exp_dt_tmp.required"=>"身份证有限期不能为空",
                "wc_lbnk_no.required"=>"联行行号不能为空",
                "bus_lic_no.required"=>"营业执照号不能为空",
                "bse_lice_nm.required"=>"营业执照名不能为空",
                "bse_lice_nm.max"=>"营业执照名最长36位",
                "crp_nm.required"=>"法人姓名不能为空",
                "merc_adds.required"=>"营业执照地址不能为空",
                "bse_lice_limited.required"=>"营业执照有限期不能为空",
                "corporate_idcord.required"=>"法人身份证不能为空",
                "corporate_idlimited.required"=>"法人身份证有限期不能为空",
                "stoe_nm.required"=>"签购单名称不能为空",
                "stoe_cnt_nm.required"=>"联系人名称不能为空",
                "stoe_cnt_tel.required"=>"联系人手机号不能为空",
                "mcc_cd.required"=>"商户类型不能为空",
                "stoe_area_cod.required"=>"地区码不能为空",
                "stoe_adds.required"=>"商户地址不能为空",
                "fee_rat.required"=>"借记卡费率不能为空",
                "max_fee_amt.required"=>"借记卡封顶不能为空",
                "fee_rat1.required"=>"贷记卡费率不能为空",
                "fee_rat_scan.required"=>"扫码费率不能为空",
                "fee_rat1_scan.required"=>"银联二维码费率不能为空",
                "org_no.max"=>"机构号最大长度为6",
                "stl_oac.max"=>"结算账户最长为23位",
                "stl_oac.min"=>"结算账户最短为1位",
                'icrp_id_no.max'=>"身份证号必须为18位",
                "crp_exp_dt_tmp.date"=>"身份证有限期必须为日期类型",
                "wc_lbnk_no.max"=>"联行行号必须为12位",
                "bus_lic_no.max"=>"营业执照号最长为27位",
                "bus_lic_no.min"=>"营业执照号最短为1位",
                "bse_lice_limited.date"=>"营业执照有限期必须为日期类型",
                "bnk_acnm.between"=>"账户必须为2到45个字符之间",
                "crp_nm.between"=>"法人姓名必须在2到10个字符之间",
                "corporate_idcord.max"=>"法人身份证号必须为18位",
                "corporate_idlimited.date"=>"法人身份证有效期必须为日期类型",
                "stoe_nm.between"=>"签购单名称必须在8到20个字符之间",
                "stoe_cnt_tel.max"=>"联系人手机号必须为11位",
                "stoe_cnt_nm.between"=>"联系人名称必须在2到10位之间",
                "stoe_area_cod.max"=>"地区码必须为6位",

            ];
            $validator = Validator::make($data, $rules, $messages);
            if($validator->errors()->get("org_no")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("org_no")
                ]);
            }
            if($validator->errors()->get("stl_oac")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("stl_oac")
                ]);
            }

              if($validator->errors()->get(" bse_lice_nm")){
                  return json_encode([
                      "status_code" => 0,
                      "message" =>$validator->errors()->get("bse_lice_nm")
                  ]);
              }
            if($validator->errors()->get("icrp_id_no")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("icrp_id_no")
                ]);
            }

            if($validator->errors()->get("crp_exp_dt_tmp")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("crp_exp_dt_tmp")
                ]);
            }
            if($validator->errors()->get("wc_lbnk_no")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("wc_lbnk_no")
                ]);
            }
            if($validator->errors()->get("bus_lic_no")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("bus_lic_no")
                ]);
            }
            if($validator->errors()->get("bse_lice_limited")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("bse_lice_limited")
                ]);
            }
            if($validator->errors()->get("bnk_acnm")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("bnk_acnm")
                ]);
            }
            if($validator->errors()->get("crp_nm")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("crp_nm")
                ]);
            }
            if($validator->errors()->get("merc_adds")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("merc_adds")
                ]);
            }
            if($validator->errors()->get("corporate_idcord")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("corporate_idcord")
                ]);
            }
            if($validator->errors()->get("corporate_idlimited")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("corporate_idlimited")
                ]);
            }
            if($validator->errors()->get("stoe_nm")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("stoe_nm")
                ]);
            }
            if($validator->errors()->get("stoe_cnt_nm")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("stoe_cnt_nm")
                ]);
            }
            if($validator->errors()->get("stoe_cnt_tel")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("stoe_cnt_tel")
                ]);
            }
            if($validator->errors()->get("stoe_area_cod")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("stoe_area_cod")
                ]);
            }
            if($validator->errors()->get("mcc_cd")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("mcc_cd")
                ]);
            }
            if($validator->errors()->get("stoe_adds")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("stoe_adds")
                ]);
            }
            if($validator->errors()->get("max_fee_amt")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("max_fee_amt")
                ]);
            }
            if($validator->errors()->get("fee_rat1")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("fee_rat1")
                ]);
            }
            if($validator->errors()->get("fee_rat_scan")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("fee_rat_scan")
                ]);
            }
            if($validator->errors()->get("fee_rat1_scan")){
                return json_encode([
                    "status_code" => 0,
                    "message" =>$validator->errors()->get("fee_rat1_scan")
                ]);
            }
            //系统参数放入GET请求串
            $url = $this->url;
//        dd(json_encode($sysParams,true));
            //发起HTTP请求

                $resp = $this->curl($url,$data);//
            // 将返回结果转换本地文件编码
            $respObject = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);

            $respObject=json_decode($respObject);
            if($respObject->repCode=="000000"){
                $data['user_id']=$request->get("user_id");
                $data['store_id']="n".date("YmdHis").time().rand(1000,999);
                if( MercRegist::create($data)){
                    return json_encode([
                        "status_code"=>$respObject->repCode,
                        "message"=>$respObject->repMsg
                    ]);
                }else{
                    return json_encode([
                        "status_code"=>0,
                        "message"=>"商户进件失败"
                    ]);
                }

            }else{
                return json_encode([
                    "status_code"=>$respObject->repCode,
                    "message"=>$respObject->repMsg
                ]);
            }


        }catch(\Exception $e){
            return json_encode([
                "status_code"=>$e->getCode(),
                "message"=>$e->getMessage()
            ]);
        }
    }
}
?>