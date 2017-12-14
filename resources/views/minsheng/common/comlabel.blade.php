        <a class="J_menuItem" href="{{route('ms_QrLists')}}"><button type="button" class="btn btn-outline btn-default">我的商户码</button></a>
        <a class="J_menuItem" href="{{route('ms_storelist')}}"><button type="button" class="btn btn-outline btn-default">商户列表</button></a>
        <a class="J_menuItem" href="{{route('ms_order')}}"><button type="button" class="btn btn-outline btn-default">商户流水</button></a>
        @permission('msConfig')
        <a class="J_menuItem" href="{{route('ms_config')}}"><button type="button" class="btn btn-outline btn-default">民生银行通道配置</button></a>
        @endpermission