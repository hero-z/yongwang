<?php
namespace App\Http\Controllers\Push;
use Illuminate\Support\Facades\Log;

class Uprint{
    function add_order($printer_sn, $order_id, $msg) {
         // 1. 修改设备序列号为网页中添加的序列号名；
        // 2. 需要添加发送此订单的服务器IP到订单服务器列表；
        // 3. 当需要打印订单时，调用add_order()；
        $UYIN_SERVER = "printer.showutech.com";
       // global $UYIN_SERVER;
        $url = "http://$UYIN_SERVER/api/2/service/add_order/$printer_sn/$order_id/";
        $data = array('data' => $msg);

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}
?>