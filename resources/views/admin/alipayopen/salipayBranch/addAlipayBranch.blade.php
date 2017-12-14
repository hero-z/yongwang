@extends('layouts.publicStyle')
@section('css')
@endsection
@section('content')
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>添加分店</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="{{url('admin/alipayopen/createSalipayBranch')}}" method="post">
                            {{csrf_field()}}
                            <input type="hidden" id="pid" name="pid" value="<?php echo $_GET['pid']?>">
                            <input type="hidden" name="store_id" value="<?php echo 's' . date('Ymdhis', time()) . rand(10000, 99999);?>">
                            <div class="form-group">
                                <label>分店名称</label>
                                <input class="form-control" type="text" value="" required="required"
                                       name="main_shop_name" id="main_shop_name">
                            </div>
                            @if ($errors->has('main_shop_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('main_shop_name') }}</strong>
                                    </span>
                            @endif
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label>联系方式:</label>
                                <input class="form-control" type="text" value="" required="required" name="contact_number"
                                       id="contact_number">
                            </div>
                            @if ($errors->has('contact_number'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('contact_number') }}</strong>
                                    </span>
                            @endif
                            <div>
                                <button class="btn btn-sm btn-primary pull-right m-t-n-xs"
                                        type="submit">
                                    <strong>保存</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('js')
@endsection
@endsection