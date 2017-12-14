<?php

namespace App\Http\Controllers\MinSheng;


use App\Http\Controllers\Tools\Aes;
use App\Http\Controllers\Tools\Tools;
use App\Http\Controllers\Tools\RSA;
use App\Models\Order;
use Illuminate\Support\Facades\DB;


/*
	调用民生银行接口的类
*/

class MinSheng
{


    static function log($data, $filename = './minshengminshengminsheng.txt')
    {
        // return;
        file_put_contents($filename, date('Y-m-d H:i:s') . "\n" . var_export($data, true) . "\n\n", FILE_APPEND);
    }


////////////////////////////////////初始化工具////START///////////////////////////////////////////////////////////////////
    private function __construct()
    {
    }

    public static $obj;

// 保存工具对象
    public static $rsa;
    public static $aes;

    public static function start()
    {
        // 初始化rsa类
        if (empty(self::$rsa))
            self::$rsa = RSA::start();
        // 初始化aes类
        if (empty(self::$aes))
            self::$aes = Aes::start();

        if (empty(self::$obj))
            self::$obj = new self();
        return self::$obj;
    }

////////////////////////////////////初始化工具////END///////////////////////////////////////////////////////////////////

    // 流水号生成
    public static function randnum()
    {
        return date('YmdHis') . mt_rand(100000, 999999);
    }

    /*
        民生进件资料
        返回
        ['status'=>'3','message'=>'进件成功，通过审核']
        ['status'=>'2','message'=>'进件成功']
        ['status'=>'1','message'=>'进件失败']
    */
    public $drawFee;
    public $tradeRate;
    public $request_url;//请求地址

    public function makeInfo($data, $pay_way_merchant_id, $region)
    {
        try {
            if(isset($data['contactName'])){
                $contactName=$data['contactName'];
            }else{
                $contactName=$data['accName'];
            }
            $cin = [
                'merchant' => [
                    'head' => [
                        'version' => '1.0',
                        'msgType' => '01',
                        'reqDate' => $data['date'],//请求日期
                    ],
                    'body' => [
                        'payWay' => $data['payWay'],//支付通道有：支付宝支付ZFBZF  微信支付WXZF
                        'merchantId' => $pay_way_merchant_id,//服务商生成的商户号
                        'merchantName' => $data['merchantName'],//营业执照上的商户名称
                        'shortName' => $data['shortName'],
                        'provinceCode' => $region['province'],
                        'cityCode' => $region['city'],
                        'districtCode' => $region['district'],
                        'merchantAddress' => $data['merchantAddress'],
                        'servicePhone' => $data['servicePhone'],//客服电话
                        'contactType' => $data['usertype'],
                        'contactName' => $contactName,//
                        /*
                                'orgCode'=>'可选参数--组织机构代码',//
                                'contactName'=>'可选参数--联系人名称',//
                                'contactPhone'=>'可选参数--联系人电话',//
                                'contactMobile'=>'可选参数--联系人手机号',//
                                'contactEmail'=>'可选参数--联系人邮箱',//
                                */

                        'category' => $data['category'],//经营类目（区分支付宝和微信）---参见附录

                        'idCard' => $data['idCard'],//商户身份证信息
                        /*		'merchantLicense'=>'可选参数--商户营业执照号',*/

                        'accNo' => $data['accNo'],//收款人账户号--直清必填
                        'accName' => $data['accName'],//收款人账户名--直清必填
                        /*
                                    'bankType'=>'kbin',//收款人账户联行号---不在附录内的银行卡必须上传
                                    'bankName'=>'工商银行',//收款人账户联行号---不在附录内的银行卡必须上传
                                            */
                        't0drawFee' => $this->drawFee,//直清提现必填---所有扣率不得小于合作方与民生签约的值
                        't0tradeRate' => $this->tradeRate,//直清提现必填

                        't1drawFee' => $this->drawFee,//T1直清必填
                        't1tradeRate' => $this->tradeRate,//T1直清必填
                    ]
                ],
            ];

            if (isset($data['bank_type']) && !empty($data['bank_type'])) {
                $cin['merchant']['body']['bankType'] = $data['bank_type'];
            }
            if (isset($data['bank_name']) && !empty($data['bank_name'])) {
                $cin['merchant']['body']['bankName'] = $data['bank_name'];

            }
            ksort($cin);
            $xml = Tools::makeXml($cin);
            self::log($cin);

            //设置aes秘钥
            self::$aes->_secrect_key = self::$aes->makeAesKey();
            // 最终上传的报文
            $finaldata = [
                'encryptData' => self::$aes->encrypt($xml),
                'encryptKey' => self::$rsa->encrypt(self::$aes->_secrect_key),
                'cooperator' => $data['cooperator'],//这个指T0还是T1  每个服务商是不一样的，具体看服务商配置
                'signData' => self::$rsa->sign($xml),
                'tranCode' => 'SMZF001',//哪种民生接口
                'callBack' => $data['callBack'],
                'reqMsgId' => md5($pay_way_merchant_id),//流水号，即订单号----不超过32位
                'ext' => $data['ext']
            ];
            self::log($finaldata);

            $r = Tools::curl($this->request_url, $finaldata);

            if (!$r) {
                return ['status' => '1', 'message' => '民生接口未返回数据！'];
                echo '接口异常！';
                die;
            }

            $r = json_decode($r, true);
// print_r($r);
            // 1.提取秘钥--rsa解密
            $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
            // 2.解开加密数据--aes解密
            $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
            // 3.验证签名是否正确--rsa验签
            self::log($xmldata);
            $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
            // 4.我方的业务处理--将提取出的xml数据入库处理等
            if (!$checksign) {
                return ['status' => '1', 'message' => '验签失败，可能被入侵！'];
                echo '验签失败';
                die;
            }
            $rdata = Tools::xml_to_array($xmldata);
            self::log($rdata);
// file_put_contents(storage_path().'/logs/chongfuchongfuchongfuchongfuchongfuchongfuchongfuchongfu.txt', var_export($rdata,true),FILE_APPEND);
// print_r($rdata);

            // 商户入驻：你们先判断同步返回，如果是S　或者E，就只有同步返回，如果是R，再等待异步通知或者主动进行商户查询。
            // 有异步返回
            if (isset($rdata['message']['head']['respType']) && $rdata['message']['head']['respType'] == 'R') {
                return ['status' => '2', 'message' => '进件已提交，等待民生审核通过！'];
            } // 没有异步返回
            elseif (isset($rdata['message']['head']['respType']) && $rdata['message']['head']['respType'] == 'S') {
                return ['status' => '3', 'message' => '入驻成功！', 'merchantCode' => $rdata['message']['body']['merchantCode']];
            } // 没有同步返回，用于提示错误
            elseif (isset($rdata['message']['head']['respType']) && $rdata['message']['head']['respType'] == 'E') {
                return ['status' => '1', 'message' => '进件失败：' . $rdata['message']['head']['respMsg']];
            }

            return ['status' => '1', 'message' => '进件资料提交失败！'];


        } catch (\Exception $e) {
            return ['status' => '1', 'message' => '进件失败！' . $e->getMessage() . $e->getLine()];

        }

    }

    /*
        民生进件资料修改
    */
    public function saveInfo($data)
    {

        // var_dump($data);die;
        if(isset($data['contact_name'])){
            $contactName=$data['contact_name'];
        }else{
            $contactName=$data['store_user'];
        }
        try {
            $cin = [
                'merchant' => [
                    'head' => [
                        'version' => '1.0',
                        'msgType' => '01',
                        'reqDate' => $data['date'],//请求日期
                    ],
                    'body' => [
                        'merchantId' => $data['rand_id'],//服务商生成的商户号
                        'shortName' => $data['store_short_name'],
                        'merchantAddress' => $data['store_address'],
                        'servicePhone' => $data['store_phone'],//客服电话
                        'category' => $data['category'],//经营类目（区分支付宝和微信）---参见附录
                        'idCard' => $data['id_card'],//商户身份证信息
                        'contactType' => $data['usertype'],
                        'contactName' => $contactName,

                        'provinceCode' => $data['province'],
                        'cityCode' => $data['city'],
                        'districtCode' => $data['district'],

                        'accNo' => $data['bank_no'],//收款人账户号--直清必填
                        'accName' => $data['store_user'],//收款人账户名--直清必填
                        't0drawFee' => $this->drawFee,//直清提现必填---所有扣率不得小于合作方与民生签约的值
                        't0tradeRate' => $this->tradeRate,//直清提现必填

                        't1drawFee' => $this->drawFee,//T1直清必填
                        't1tradeRate' => $this->tradeRate,//T1直清必填
                    ]
                ],
            ];
            if (isset($data['bank_type']) && !empty($data['bank_type'])) {
                $cin['merchant']['body']['bankType'] = $data['bank_type'];
            }
            if (isset($data['bank_name']) && !empty($data['bank_name'])) {
                $cin['merchant']['body']['bankName'] = $data['bank_name'];

            }
            ksort($cin);
            $xml = Tools::makeXml($cin);

            //设置aes秘钥
            self::$aes->_secrect_key = self::$aes->makeAesKey();
            // 最终上传的报文
            $finaldata = [
                'encryptData' => self::$aes->encrypt($xml),
                'encryptKey' => self::$rsa->encrypt(self::$aes->_secrect_key),
                'cooperator' => $data['cooperator'],//这个指T0还是T1  每个服务商是不一样的，具体看服务商配置
                'signData' => self::$rsa->sign($xml),
                'tranCode' => 'SMZF009',//哪种民生接口
                'callBack' => $data['callBack'],
                'reqMsgId' => self::randnum(),//流水号，即订单号----不超过32位
                'ext' => ''
            ];

            $r = Tools::curl($this->request_url, $finaldata);

            if (!$r) {
                return ['status' => '1', 'message' => '民生接口未返回数据！'];
                echo '接口异常！';
                die;
            }

            $r = json_decode($r, true);
// print_r($r);
            // 1.提取秘钥--rsa解密
            $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
            // 2.解开加密数据--aes解密
            $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
            // 3.验证签名是否正确--rsa验签
            $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
            // 4.我方的业务处理--将提取出的xml数据入库处理等
            if (!$checksign) {
                return ['status' => '1', 'message' => '验签失败，可能被入侵！'];
                echo '验签失败';
                die;
            }
            $rdata = Tools::xml_to_array($xmldata);
            file_put_contents(storage_path() . '/logs/chongfuchongfuchongfuchongfuchongfuchongfuchongfuchongfu.txt', var_export($rdata, true), FILE_APPEND);
// print_r($rdata);

            // 商户入驻：你们先判断同步返回，如果是S　或者E，就只有同步返回，如果是R，再等待异步通知或者主动进行商户查询。
            // 有异步返回
            if (isset($rdata['message']['head']['respType']) && $rdata['message']['head']['respType'] == 'R') {
                return ['status' => '2', 'message' => '资料修改正在审核！'];
            } // 没有异步返回
            elseif (isset($rdata['message']['head']['respType']) && $rdata['message']['head']['respType'] == 'S') {
                return ['status' => '3', 'message' => '资料修改成功！'];
            } // 没有同步返回，用于提示错误
            elseif (isset($rdata['message']['head']['respType']) && $rdata['message']['head']['respType'] == 'E') {
                return ['status' => '1', 'message' => '资料修改失败：' . $rdata['message']['head']['respMsg']];
            }

            return ['status' => '1', 'message' => '资料修改失败！'];
        } catch (\Exception $e) {
            return ['status' => '1', 'message' => '资料修改失败：' . $e->getMessage() . $e->getLine()];

        }


    }


    /*

        解开民生返回的数据
        入参
        [
            'encryptData'=>'加密数据',
            'encryptKey'=>'aes秘钥',
            'tranCode'=>'民生的哪种接口',
            'reqMsgId'=>'民生流水号',
            'cooperator'=>'服务商t0还是t1',
            'signData'=>'民生签名',

        ]

        返回
            ['status'=>'1','message'=>'数据解锁失败']
            ['status'=>'2','message'=>'数据成功解锁','data'=>'数据']
    */
    public function unlockData($r)
    {
        try {
            // 1.提取秘钥--rsa解密
            $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
            // 2.解开加密数据--aes解密
            $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
            // 3.验证签名是否正确--rsa验签
            $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
            // 4.我方的业务处理--将提取出的xml数据入库处理等
            if (!$checksign) {
                return ['status' => '1', 'message' => '验签失败，可能被入侵！'];
                echo '验签失败';
                die;
            }
            $rdata = Tools::xml_to_array($xmldata);
            return ['status' => '2', 'message' => '数据已解锁', 'data' => $rdata];
        } catch (\Exception $e) {

            return ['status' => '1', 'message' => '数据解锁失败:' . $e->getMessage() . $e->getLine()];
        }
    }


    /*
        4.2.2扫码支付：SMZF002

        测试

        // 接口工具参数准备
        $ms=MinSheng::start();
        $config=DB::table('ms_configs')->where('id','=','1')->first();
        MinSheng::$rsa->self_public_key=MinSheng::$rsa->matePubKey($config->self_public_key);
        MinSheng::$rsa->self_private_key=MinSheng::$rsa->matePriKey($config->self_private_key);
        MinSheng::$rsa->third_public_key=MinSheng::$rsa->matePubKey($config->third_public_key);

        $config = AlipayIsvConfig::where('id', 1)->first();//支付宝配置信息app_id
        $ms->request_url='http://110.80.39.174:9013/nbp-smzf-hzf';
        $r=$ms->alipay($config->pid);



    */
    public function scanPay($pid)
    {
        $date = date('YmdHis');
        $merchant_id = '2017050405307206';
        $cin = [
            'merchant' => [
                'head' => [
                    'version' => '1.0',
                    'msgType' => '01',
                    'reqDate' => $date,//请求日期
                ],
                'body' => [
                    'merchantCode' => $merchant_id,//支付宝商编
                    'totalAmount' => '0.01',//单位元
                    'subject' => '显示在支付宝订单上的订单标题',//营业执照上的商户名称
                    'desc' => '订单描述',
                    'operatorId' => '操作员编号',
                    'storeId' => 'm20170504100912888117',
                    'terminalId' => '商户机具终端编号',
                    // 'limitPay'=>'1',
                    'source' => $pid,
                ]
            ],
        ];

        ksort($cin);
        $xml = Tools::makeXml($cin);

        //设置aes秘钥
        self::$aes->_secrect_key = self::$aes->makeAesKey();
        // 最终上传的报文
        $finaldata = [
            'encryptData' => self::$aes->encrypt($xml),
            'encryptKey' => self::$rsa->encrypt(self::$aes->_secrect_key),
            'cooperator' => 'SMZF_YMXKJ_T0',//这个指T0还是T1  每个服务商是不一样的，具体看服务商配置
            'signData' => self::$rsa->sign($xml),
            'tranCode' => 'SMZF002',//哪种民生接口
            'callBack' => url('api/minsheng/infonotify'),
            'reqMsgId' => md5($date),//流水号，即订单号----不超过32位
            'ext' => ''
        ];
// print_r($finaldata);

        $r = Tools::curl($this->request_url, $finaldata);
        $r = json_decode($r, true);

        // 1.提取秘钥--rsa解密
        $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
        // 2.解开加密数据--aes解密
        $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
        // 3.验证签名是否正确--rsa验签
        $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
        // 4.我方的业务处理--将提取出的xml数据入库处理等
        if (!$checksign) {
            return ['status' => '1', 'message' => '验签失败，可能被入侵！'];
            echo '验签失败';
            die;
        }
        $rdata = Tools::xml_to_array($xmldata);


// print_r($rdata);
        return $rdata['message']['body']['qrCode'];


    }

    /*
        支付宝微信的服务窗或者h5支付

        入参
            $type=wx微信支付    zfb支付宝支付   qq支付

            $data
        返回
            ['status'=>'1','message'=>'下单失败']

    */
    public function webPay($type, $data)
    {
        $message = '';
        try {
            $cin = [
                'merchant' => [
                    'head' => [
                        'version' => '1.0',
                        'msgType' => '01',
                        'reqDate' => $data['date'],//请求日期
                    ],
                    'body' => [
                        'merchantCode' => $data['merchant_id'],//支付宝商编
                        'totalAmount' => $data['totalAmount'],//单位元
                        'subject' => $data['subject'],//
                        'desc' => $data['desc'],
                        'operatorId' => $data['operatorId'],
                        'storeId' => $data['storeId'],
                        'terminalId' => '商户机具终端编号',
                        // 'limitPay'=>'1',

                        // 'subAppid'=>'微信公众号',//微信公众号---需要事先报民生
                        // 'userId'=>$data['userId'],//支付宝用户标识；微信合作方标识；qq不需要
                        // 'source'=>$pid,//支付宝pid
                        // 'goodsTag'=>'微信上送，活动',//微信上送，活动
                    ]
                ],
            ];

            switch ($type) {

                case 'zfb':
                    $cin['merchant']['body']['userId'] = $data['userId'];
                    $cin['merchant']['body']['source'] = $data['pid'];
                    break;

                case 'wx':
                    $cin['merchant']['body']['userId'] = $data['userId'];
                    $cin['merchant']['body']['subAppid'] = $data['subAppid'];
                    // $cin['merchant']['body']['goodsTag']=$data['goodsTag'];
                    break;

                case 'qq':
                    break;

                default:
                    return ['status' => '1', 'message' => '下单失败'];
                    break;
            }

            ksort($cin);
            file_put_contents(storage_path() . '/logs/ssssssssssss.txt', var_export($cin, true), FILE_APPEND);
            $xml = Tools::makeXml($cin);

            //设置aes秘钥
            self::$aes->_secrect_key = self::$aes->makeAesKey();
            // 最终上传的报文
            $finaldata = [
                'encryptData' => self::$aes->encrypt($xml),
                'encryptKey' => self::$rsa->encrypt(self::$aes->_secrect_key),
                'cooperator' => $data['cooperator'],//这个指T0还是T1  每个商户是不一样的，具体看服务商配置
                'signData' => self::$rsa->sign($xml),
                'tranCode' => 'SMZF010',//哪种民生接口
                'callBack' => $data['callBack'],
                'reqMsgId' => $data['reqMsgId'],//流水号，即订单号----不超过32位
                'ext' => ''
            ];
            file_put_contents(storage_path() . '/logs/ssssssssssss.txt', var_export($finaldata, true), FILE_APPEND);
            // print_r($finaldata);
// file_put_contents('./ssssssssssss.txt', var_export($finaldata,true),FILE_APPEND);

            $r = Tools::curl($this->request_url, $finaldata);
            if (!$r) {
                return ['status' => '1', 'message' => '民生银行接口断开！'];

            }
            file_put_contents(storage_path() . '/logs/kkkkkkkkkkkkkk.txt', var_export($r, true), FILE_APPEND);
            $r = json_decode($r, true);


            // 1.提取秘钥--rsa解密
            $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
            // 2.解开加密数据--aes解密
            $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
            // 3.验证签名是否正确--rsa验签
            $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
            // 4.我方的业务处理--将提取出的xml数据入库处理等
            if (!$checksign) {
                return ['status' => '1', 'message' => '验签失败，可能被入侵！'];
                echo '验签失败';
                die;
            }
            $rdata = Tools::xml_to_array($xmldata);
            $message .= isset($rdata['message']['head']['respMsg']) ? $rdata['message']['head']['respMsg'] : '';
            file_put_contents(storage_path() . '/logs/kkkkkkkkkkkkkk.txt', var_export($rdata, true), FILE_APPEND);
            if ($rdata['message']['head']['respType'] == 'R' || $rdata['message']['head']['respType'] == 'S') {
                switch ($type) {
                    case 'zfb':
                        return ['status' => '2', 'message' => '订单创建成功，请完成支付！', 'data' => ['channelNo' => $rdata['message']['body']['channelNo']]];
                        break;

                    case 'wx':
                        return ['status' => '2', 'message' => '订单创建成功，请完成支付！', 'data' => $rdata['message']['body']['wxjsapiStr'], 'prepayId' => $rdata['message']['body']['prepayId']];

                        break;

                    case 'qq':
                        return ['status' => '2', 'message' => '订单创建成功，请完成支付！', 'data' => ['prepayId' => $rdata['message']['body']['prepayId']]];
                        break;
                }
            }
        } catch (\Exception $e) {
            return ['status' => '1', 'message' => '异常：' . $e->getMessage() . $e->getLine()];
        }

        return ['status' => '1', 'message' => '订单创建失败，请重新下单！' . $message];


    }

    //设置微信支付目录和设置公众号
    public static function setforeach($store_id)
    {
        // 接口工具参数准备
        $ms = self::start();
        $config = DB::table('ms_configs')->where('id', '=', '1')->first();
        $store = DB::table('ms_pay_way')->where('store_id', $store_id)->where('pay_way', 'WXZF')->first();
        if (empty($store)) {
            return;

        }
        $ms->request_url = $config->request_url;
        self::$rsa->self_public_key = self::$rsa->matePubKey($config->self_public_key);
        self::$rsa->self_private_key = self::$rsa->matePriKey($config->self_private_key);
        self::$rsa->third_public_key = self::$rsa->matePubKey($config->third_public_key);
        $arr = [
            'a' => ['zdlx' => 2, 'zdsj' => $config->wx_app_id],
            'b' => ['zdlx' => 1, 'zdsj' => url('/api/minsheng') . '/']

        ];
        $return = [];
        foreach ($arr as $k => $v) {
            $return[$k] = $ms->wxsubappidConfig($store_id, $store->merchant_id, $v);
        }
        return $return;

    }


    public function wxsubappidConfig($store_id, $mid, $data, $callback = '')
    {
        $config = DB::table('ms_stores')->where('store_id', '=', $store_id)->first();
        try {
            $cin = [
                'merchant' => [
                    'head' => [
                        'version' => '1.0',
                        'msgType' => '01',
                        'reqDate' => date('YmdHis'),//请求日期
                    ],
                    'body' => [
                        'merchantCode' => $mid,//服务商生成的商户号
                        'zdlx' => $data['zdlx'],
                        'zdsj' => $data['zdsj'],
                    ]
                ],
            ];
            file_put_contents(storage_path() . '/logs/111.txt', var_export($cin, true), FILE_APPEND);

            ksort($cin);
            $xml = Tools::makeXml($cin);
            //设置aes秘钥
            self::$aes->_secrect_key = self::$aes->makeAesKey();

            // 最终上传的报文
            $finaldata = [
                'encryptData' => self::$aes->encrypt($xml),
                'encryptKey' => self::$rsa->encrypt(self::$aes->_secrect_key),
                'cooperator' => $config->cooperator,//这个指T0还是T1  每个服务商是不一样的，具体看服务商配置
                'signData' => self::$rsa->sign($xml),
                'tranCode' => 'SMZF030',//哪种民生接口
                'callBack' => $callback,
                'reqMsgId' => self::randnum(),//流水号，即订单号----不超过32位
                'ext' => ''
            ];

            $r = Tools::curl($this->request_url, $finaldata);
            if (!$r) {
                return ['status' => '1', 'message' => '民生接口未返回数据！'];
                echo '接口异常！';
                die;
            }

            $r = json_decode($r, true);


// print_r($r);
            // 1.提取秘钥--rsa解密
            $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
            // 2.解开加密数据--aes解密
            $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
            // 3.验证签名是否正确--rsa验签
            $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
            // 4.我方的业务处理--将提取出的xml数据入库处理等
            if (!$checksign) {
                return '验签失败';
                return ['status' => '1', 'message' => '验签失败，可能被入侵！'];
                echo '验签失败';
                die;
            }
            $rdata = Tools::xml_to_array($xmldata);

            // 商户入驻：你们先判断同步返回，如果是S　或者E，就只有同步返回，如果是R，再等待异步通知或者主动进行商户查询。
            if (isset($rdata['message']['head']['respType']) && $rdata['message']['head']['respType'] == 'S') {
                return true;
                return ['status' => '3', 'message' => '资料修改成功！'];
            } // 没有同步返回，用于提示错误
            else {
                return $rdata['message']['head']['respMsg'];
                return ['status' => '1', 'message' => '资料修改失败：' . $rdata['message']['head']['respMsg']];
            }

        } catch (\Exception $e) {
            return $e->getMessage();
            return ['status' => '1', 'message' => '资料修改失败：' . $e->getMessage() . $e->getLine()];

        }
    }

//扫码强入库
    public function pay($type,$authCode, $totalAmount, $store_id, $mid, $data, $merchant_id, $callback = '')
    {
        $out_trade_no = date('YmdHis', time()) . rand(10000, 99999);
        $config = DB::table('ms_stores')->where('store_id', '=', $store_id)->first();
        try {
            $cin = [
                'merchant' => [
                    'head' => [
                        'version' => '1.0',
                        'msgType' => '01',
                        'reqDate' => date('YmdHis'),//请求日期
                    ],
                    'body' => [
                        'merchantCode' => $mid,//服务商生成的商户号
                        'scene' => '1',
                        'authCode' => $authCode,
                        'totalAmount' => $totalAmount,
                        'subject' => $data['subject'],//
                        'desc' => $data['desc'],
                        'operatorId' => $data['operatorId'],
                        'storeId' => $data['storeId'],
                        'terminalId' => '商户机具终端编号',
                    ]
                ],
            ];
            ksort($cin);
            $xml = Tools::makeXml($cin);
            //设置aes秘钥
            self::$aes->_secrect_key = self::$aes->makeAesKey();
            // 最终上传的报文
            $finaldata = [
                'encryptData' => self::$aes->encrypt($xml),
                'encryptKey' => self::$rsa->encrypt(self::$aes->_secrect_key),
                'cooperator' => $config->cooperator,//这个指T0还是T1  每个服务商是不一样的，具体看服务商配置
                'signData' => self::$rsa->sign($xml),
                'tranCode' => 'SMZF003',//哪种民生接口
                'callBack' => url('api/minsheng/paynotify'),
                'reqMsgId' => $out_trade_no,//流水号，即订单号----不超过32位
                'ext' => ''
            ];
            $msconfig = DB::table('ms_configs')->where('id', '=', '1')->first();
            $r = Tools::curl($msconfig->request_url, $finaldata);
            if (!$r) {
                return json_encode([
                    'status' => 0,
                    'msg' => '接口异常'
                ]);
            }

            $r = json_decode($r, true);


// print_r($r);
            // 1.提取秘钥--rsa解密
            $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
            // 2.解开加密数据--aes解密
            $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
            // 3.验证签名是否正确--rsa验签
            $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
            // 4.我方的业务处理--将提取出的xml数据入库处理等
            if (!$checksign) {
                return json_encode([
                    'status' => 0,
                    'msg' => '商户信息通道出错！请检测配置是否正确'
                ]);
            }
            $rdata = Tools::xml_to_array($xmldata);
            // 你们先判断同步返回，如果是S　或者E，就只有同步返回，如果是R，再等待异步通知或者主动进行商户查询。
            if (isset($rdata['message']['head']['respType']) && $rdata['message']['head']['respType'] == 'S') {
                //同步支付成功
                Order::create([
                    'out_trade_no' => $out_trade_no,
                    'trade_no' => $rdata['message']['body']['channelNo'],
                    'store_id' => $store_id,
                    'total_amount' => $totalAmount,
                    'status' => 'TRADE_SUCCESS',
                    'pay_status' => 1,
                    'merchant_id' => $merchant_id,
                    'type' => $type
                ]);
                return json_encode([
                    'status' => 1,
                    'msg' => '支付成功',
                    'out_trade_no' => $out_trade_no,
                    'trade_no' => $rdata['message']['body']['channelNo'],
                ]);
            } // 没有同步返回，用于提示错误
            else {
                //同步支付成功
                Order::create([
                    'out_trade_no' => $out_trade_no,
                    'trade_no' => $rdata['message']['body']['channelNo'],
                    'store_id' => $store_id,
                    'total_amount' => $totalAmount,
                    'status' => 'TRADE_PAYING',
                    'pay_status' => 2,
                    'merchant_id' => $merchant_id,
                    'type' => $type,

                ]);
                $i = 5;//循环35次
                for ($a = 1; $a < $i; $a++) {
                    sleep(1);
                    $status =$this->searchOrder($msconfig->request_url,$out_trade_no,$store_id);
                    if ($status['message']['head']['respMsg'] == "success"&&$status['message']['body']['oriRespType']=="S") {
                        Order::where('out_trade_no', $out_trade_no)->update(
                            [
                                'status' => "TRADE_SUCCESS",
                                'pay_status' => 1,
                            ]);
                        return json_encode([
                            'status' => 1,
                            'msg' => '订单支付成功',
                            'out_trade_no' => $out_trade_no,
                            'trade_no' => $status['message']['body']['channelNo'],
                        ]);
                        break;
                    }
                    if ($status['message']['head']['respMsg'] == "success"&&$status['message']['body']['oriRespType']!="R") {
                        return json_encode([
                            'status' => 0,
                            'msg' => $status['message']['head']['respMsg']
                        ]);
                        break;
                    }

                }
                if ($status['message']['head']['respMsg'] == "success"&&$status['message']['body']['oriRespType']=="R") {
                    $chanel = $this->OrderClose($msconfig->request_url,$out_trade_no,$store_id);
                    if ($chanel['message']['head']['respType'] == 'S') {
                        Order::where('out_trade_no', $out_trade_no)->update([
                            'status' => 'TRADE_CLOSED',
                            'pay_status' => 4,
                        ]);
                        return json_encode([
                            'status' => 0,
                            'msg' => $chanel['message']['head']['respMsg']
                        ]);
                    } else {
                        return json_encode([
                            'status' => 0,
                            'msg' => $chanel['message']['head']['respMsg']
                        ]);
                    }

                }
            }

        } catch (\Exception $e) {
            return json_encode([
                'status' => 0,
                'msg' => $e->getMessage(),
            ]);
        }
    }

    public function searchOrder($url,$order_no,$store_id)
    {
        $config = DB::table('ms_stores')->where('store_id', '=', $store_id)->first();
        try {
            $cin = [
                'merchant' => [
                    'head' => [
                        'version' => '1.0',
                        'msgType' => '01',
                        'reqDate' => date('YmdHis'),//请求日期
                    ],
                    'body' => [
                        'oriReqMsgId' => $order_no,
                    ]
                ],
            ];


            ksort($cin);
            $xml = Tools::makeXml($cin);
            //设置aes秘钥
            self::$aes->_secrect_key = self::$aes->makeAesKey();

            // 最终上传的报文
            $finaldata = [
                'encryptData' => self::$aes->encrypt($xml),
                'encryptKey' => self::$rsa->encrypt(self::$aes->_secrect_key),
                'cooperator' => $config->cooperator,//这个指T0还是T1  每个服务商是不一样的，具体看服务商配置
                'signData' => self::$rsa->sign($xml),
                'tranCode' => 'SMZF006',//哪种民生接口
                'callBack' => '',
                'reqMsgId' => self::randnum(),//流水号，即订单号----不超过32位
                'ext' => ''
            ];

            $r = Tools::curl($url, $finaldata);
            if (!$r) {
                return ['status' => '1', 'message' => '民生接口未返回数据！'];
                echo '接口异常！';
                die;
            }

            $r = json_decode($r, true);
// print_r($r);
            // 1.提取秘钥--rsa解密
            $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
            // 2.解开加密数据--aes解密
            $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
            // 3.验证签名是否正确--rsa验签
            $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
            // 4.我方的业务处理--将提取出的xml数据入库处理等
            if (!$checksign) {
                return ['status' => '1', 'message' => '验签失败，可能被入侵！'];
            }
            $rdata = Tools::xml_to_array($xmldata);

            return $rdata;

        } catch (\Exception $e) {
            return ['status' => 0, 'msg'=>$e->getMessage()];

        }
    }
    public function OrderClose($url,$order_no,$store_id)
    {
        $config = DB::table('ms_stores')->where('store_id', '=', $store_id)->first();
        try {
            $cin = [
                'merchant' => [
                    'head' => [
                        'version' => '1.0',
                        'msgType' => '01',
                        'reqDate' => date('YmdHis'),//请求日期
                    ],
                    'body' => [
                        'oriReqMsgId' => $order_no,
                        'isClearOrCancel'=>'1',
                    ]
                ],
            ];


            ksort($cin);
            $xml = Tools::makeXml($cin);
            //设置aes秘钥
            self::$aes->_secrect_key = self::$aes->makeAesKey();

            // 最终上传的报文
            $finaldata = [
                'encryptData' => self::$aes->encrypt($xml),
                'encryptKey' => self::$rsa->encrypt(self::$aes->_secrect_key),
                'cooperator' => $config->cooperator,//这个指T0还是T1  每个服务商是不一样的，具体看服务商配置
                'signData' => self::$rsa->sign($xml),
                'tranCode' => 'SMZF005',//哪种民生接口
                'callBack' => '',
                'reqMsgId' => self::randnum(),//流水号，即订单号----不超过32位
                'ext' => ''
            ];

            $r = Tools::curl($url, $finaldata);
            if (!$r) {
                return ['status' => '1', 'message' => '民生接口未返回数据！'];
                echo '接口异常！';
                die;
            }

            $r = json_decode($r, true);
// print_r($r);
            // 1.提取秘钥--rsa解密
            $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
            // 2.解开加密数据--aes解密
            $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
            // 3.验证签名是否正确--rsa验签
            $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
            // 4.我方的业务处理--将提取出的xml数据入库处理等
            if (!$checksign) {
                return ['status' => '1', 'message' => '验签失败，可能被入侵！'];
            }
            $rdata = Tools::xml_to_array($xmldata);

            return $rdata;

        } catch (\Exception $e) {
            return ['status' => 0, 'msg'=>$e->getMessage()];

        }
    }
    public function searchStore($pay, $store_id)
    {
        $config = DB::table('ms_stores')->where('store_id', '=', $store_id)->first();
        try {
            $cin = [
                'merchant' => [
                    'head' => [
                        'version' => '1.0',
                        'msgType' => '01',
                        'reqDate' => date('YmdHis'),//请求日期
                    ],
                    'body' => [
                        'contactType' => str_pad($pay->usertype, 2, '0', STR_PAD_LEFT),//服务商生成的商户号
                        'merchantId' => $pay->rand_id,
                    ]
                ],
            ];


            ksort($cin);
            $xml = Tools::makeXml($cin);
            //设置aes秘钥
            self::$aes->_secrect_key = self::$aes->makeAesKey();

            // 最终上传的报文
            $finaldata = [
                'encryptData' => self::$aes->encrypt($xml),
                'encryptKey' => self::$rsa->encrypt(self::$aes->_secrect_key),
                'cooperator' => $config->cooperator,//这个指T0还是T1  每个服务商是不一样的，具体看服务商配置
                'signData' => self::$rsa->sign($xml),
                'tranCode' => 'SMZF007',//哪种民生接口
                'callBack' => '',
                'reqMsgId' => self::randnum(),//流水号，即订单号----不超过32位
                'ext' => ''
            ];

            $r = Tools::curl($this->request_url, $finaldata);
            if (!$r) {
                return ['status' => '1', 'message' => '民生接口未返回数据！'];
                echo '接口异常！';
                die;
            }

            $r = json_decode($r, true);


// print_r($r);
            // 1.提取秘钥--rsa解密
            $aeskey = (self::$rsa)->decrypt($r['encryptKey']);
            // 2.解开加密数据--aes解密
            $xmldata = (self::$aes)->decrypt($r['encryptData'], $aeskey);
            // 3.验证签名是否正确--rsa验签
            $checksign = (self::$rsa)->checksign($xmldata, $r['signData']);
            // 4.我方的业务处理--将提取出的xml数据入库处理等
            if (!$checksign) {
                return ['status' => '1', 'message' => '验签失败，可能被入侵！'];
            }
            $rdata = Tools::xml_to_array($xmldata);


            if ($rdata['message']['head']['respType'] == 'S') {
                return ['status' => '2', 'merchant_id' => $rdata['message']['body']['merchantCode']];
            }
            return ['status' => '1', 'merchant_id' => ''];


        } catch (\Exception $e) {
            return ['status' => '1', 'merchant_id' => ''];
            return $e->getMessage();
            return ['status' => '1', 'message' => '资料修改失败：' . $e->getMessage() . $e->getLine()];

        }
    }


}

