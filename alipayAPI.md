###门店推送消息格式
```
array (
  'is_online' => 'T',
  'biz_type' => 'CREATE_SHOP_AUDIT',
  'notify_time' => '2017-02-03 17:24:38',
  'shop_id' => '2017020300077000000000058522',
  'sign_type' => 'RSA',
  'notify_type' => 'shop_audit_result',
  'apply_id' => '2017020300107000000000090951',
  'version' => '2.0',
  'sign' => 'NEKK8yr+PJaXAjlPswMulB2YD9dMkuTUq08Ts2IpM44tPMDcVprwC1VwnSNV3T9QLoPHqjfw6MBovVT96ysYI4OXBBouh9/Jz/mwkO61wAS/TQDFdzi58DSWVznJxSkHkX5KsDRXC1M8nfjRHk4nYyevgYYsCsF31ljY/gs90p0=',
  'is_show' => 'T',
  'request_id' => '20170203172031',
  'notify_id' => '58bb2f6c719c70c345dd77d9852fcdclbq',
  'audit_status' => 'AUDIT_SUCCESS',
) 
```

###门店提交返回格式
####正确的返回
```
{#318
  +"alipay_offline_market_shop_create_response": {#324
    +"code": "10000"
    +"msg": "Success"
    +"audit_status": "AUDITING"
    +"apply_id": "2017020400107000000000090965"
  }
  +"sign": "CvvvtmyhpALr9Ut0vQ4FiTBUhx5M3+EET8zWsFBKpZPcqXO31A9ePNSQKH7J4LYHOi4YWJ/ShkLY1PrHr32f+HoySQNXmH5OYhsEthZb+eC7kpLymyHRhu2gCvgo5wX76wSRZyX4LtgIHBi2A7AZFnb6Pra0DkGjO96vboNBKo6AJLvt4+jEnyA2fTojtuThig3IRND1eYP+1okez3M6F1yt61x1hiqDCaa6hXud3VBDGZ0Jl0Eac9zgfyeJXF+aH+87ClHxoRpqIPMOroAgRELu0xKVqN8xUa1xY1r1oAgEjyy2mjPLkbcLvCrbHlshvuK8Ijszgd0E0Cyavui+JQ=="
}
```

####错误的返回
```
{#324
  +"alipay_offline_market_shop_create_response": {#318
    +"code": "40004"
    +"msg": "Business Failed"
    +"sub_code": "SHOP_CREATE_INVALID_PARAM"
    +"sub_msg": "店铺创建,cover不能为空"
  }
  +"sign": "bG8DoxiIjk10O2nMbsPVUaiqP3OGBL6tp23r7ka78VeU+Xny7qW4+0TKPJ9r8e3DLcsW+lyyYVZkkzFBe3nLoqu8hBSvDjxvjcdqY3aoZL3y30Rnbl3GFr8RSQ1f9XXplQQ0KV9/wUZ0hIVW7FdKv1Cxlg751WBLu7V0xCRlQzCAnmnQdRbrBmPiH6WlvrErTS3IvoIiduC2vv71M9yLxT86CML9JjJy52k5+4d0V0y12Z8x13bmsNa9hjnQA4fvNPKXZxu0yWPna6QOwTfWZpe/iZahnzIcTfejFFaudMWgm1e0oDiGZotPHWOLqOamRi/iOoYUOf59+M8yPGutyA=="
}
```