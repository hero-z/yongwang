<?php
/**
 * Created by PhpStorm.
 * User: daimingkang
 * Date: 2016/12/7
 * Time: 17:30
 */

namespace App\Http\Controllers\Api;


use Alipayopen\Sdk\Request\AlipayOfflineMaterialImageUploadRequest;
use App\Http\Controllers\UnionPay\AopClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Qiniu\Storage\UploadManager;

class PublicController extends BaseController
{

    //图片上传
    public function upload(Request $request)
    {
        $file = Input::file('image');
        $store_id = $request->get('store_id', 'default');
        $app_auth_token = $request->get('app_auth_token');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/shop/' . $store_id . '/', $newName);

        }
        //上传至支付宝
        $aop = $this->AopClient();
        $aop->apiVersion = '2.0';
        $aop->method = "alipay.offline.material.image.upload";
        $requests = new AlipayOfflineMaterialImageUploadRequest();
        $requests->setImageType($entension);
        $requests->setImageName($newName);
        $requests->setImageContent('@' . $path);
        $result = $aop->execute($requests);
        $responseNode = str_replace(".", "_", $requests->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            $data = [
                'image_id' => $result->$responseNode->image_id,
                'image_url' => url('/uploads/shop/' . $store_id . '/' . $newName),
            ];

            return json_encode($data);

        } else {
            return "上传失败";
        }
    }

    //图片上传
    public function uploadfile(Request $request)
    {
        $file = Input::file('file');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/', $newName);

        }
        $data = [
            'path' => public_path() . '/uploads/' . $newName,
            'status' => 1,
        ];

        return json_encode($data);

    }
   //更新包上传
    public function updateUrl(Request $request)
    {
        $file = Input::file('file');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            if($entension=="apk") {
                $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
                $path = $file->move(public_path() . '/app/', $newName);
                $data = [
                    'path' => '/app/' . $newName,
                    'status' => 1,
                ];
            }else{
                $data = [
                    'status' => 0,
                ];
            }
        }
        return json_encode($data);

    }
    //平安
    public function uploadImagePingAn(Request $request)
    {
        $file = Input::file('image');
        $external_id = $request->get('external_id');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.

            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/' . $external_id . '/', $newName);

        }
        $data = [
            'image_url' => url('/uploads/' . $external_id . '/' . $newName),
            'status' => 1,
        ];

        return json_encode($data);

    }

    public function uploadImageUnionPay(Request $request)
    {
        $file = Input::file('image');
        $store_id = $request->get('store_id');
        $file_type = $request->get('file_type');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/UnionPay/' . $store_id . '/', $newName);
        }
        $img_url = url('/uploads/UnionPay/' . $store_id . '/' . $newName);
        //文件上传至银联
        //换取token
        $ao = new \App\Http\Controllers\UnionPay\BaseController();
        $aop = $ao->AopClient();
        $aop->method = "fshows.paycompany.liquidation.file.uploadtoken";
        $pay = [
            'file_type' => (int)$file_type,
            'file_name' => $newName,
        ];
        $data = array('content' => json_encode($pay));
        $response = $aop->execute($data);
        $responseArray = json_decode($response, true);
        if ($responseArray['success']) {
            $upManager = new UploadManager();
            $token = $responseArray['return_value']['up_token'];
            $key = $responseArray['return_value']['file_key'];
            $return = $upManager->putFile($token, $key, $path);
            $data = [
                'image_url' => $img_url,
                'status' => 1,
                'key' => $return['0']['key']
            ];
        } else {
            $data = [
                'image_url' => '',
                'status' => 0,
                'key' => ''
            ];
        }


        return json_encode($data);
    }

    public function uploadlocal(Request $request)
    {
        $store_id = $request->get('store_id');
        $file = Input::file('image');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/' . $store_id . '/', $newName);

        }
        $data = [
            'image_url' => url('/uploads/' . $store_id . '/' . $newName),
            'status' => 1,
        ];

        return json_encode($data);
    }

    public function uploads(Request $request)
    {
        $file = Input::file('image');
        if ($file->isValid()) {
            $entension = $file->getClientOriginalExtension(); //上传文件的后缀.
            $newName = date('YmdHis') . mt_rand(100, 999) . '.' . $entension;
            $path = $file->move(public_path() . '/uploads/ad' . '/', $newName);

        }

        $data = [
            'image_url' => '/uploads/ad/' . $newName,
            'status' => 1,
        ];
        return json_encode($data);
    }

}
