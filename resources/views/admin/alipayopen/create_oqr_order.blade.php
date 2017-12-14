@extends('layouts.koubei')
@section('title')
    {{$shop['auth_shop_name']}}
@endsection
@section('css')

    <!--<link href="{{asset('/css/weixinpay/wxpay.css')}}" rel="stylesheet">-->
    	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
    	<style type="text/css">
    		@-webkit-keyframes shine{
	from{
		opacity:0
		}to{
			opacity:1
			}}
@keyframes shine{
	from{
	opacity:0
	}to{
		opacity:1
		}}
    		
    		.ipt{
	font-size:18px;
	border-bottom:1px solid #ccc;
	/*border-radius:4px;*/
	/*line-height:30px;*/
	padding:10px;
	/*padding-bottom: -10px;*/
	position:relative;
	color:#ccc;
	margin-top:20px !important;
	}
.ipt i{
	width:2px;
	height:40px;
	background:#0c97ea;
	display:block;
	position:absolute;
	right:10px;
	top:15px;
	-webkit-animation:shine 1.5s infinite ease;
	animation:shine 1.5s infinite ease}
	.ipt .price{
		float:right;
		width:60%;
		text-align:right;
		font-size:30px;
		padding-right:10px;
		/*background:#1aac19;*/
		z-index: 1;
		}
	.ipt .price span{
		color:#000;
		
		margin-left:5px
		
		
		}
    .keyboard{
	position:fixed;
	height:40%;
	bottom:0;
	left:0;
	width:100%;
	right:0;
	z-index:11
	}
	.keyboard ul{
		width:100%;
		height:100%
		}
	.keyboard ul li{
		width:25%;
		cursor:pointer;
		list-style:none;
		box-sizing:border-box;
		line-height:1;
		height:25%;
		position:relative;
		float:left;
		border:1px solid #ccc;
		border-width:1px 0 0 1px;
		background-color:#fff;
		-webkit-tap-highlight-color:transparent
		}
	.keyboard ul li:after{
		content:attr(data-num);
		position:absolute;
		top:50%;
		left:0;
		width:100%;
		font-size:20px;
		text-align:center;
		color:#333;
		-webkit-transform:translateY(-50%);
		}
		.keyboard ul li.folded{
			background:#d4d8db;
			background-size:auto 50%;
			}
			
			.keyboard ul li.del img{
				padding-left: 20%;
				padding-top: 5%;
				width: 60%;
				height: 80%;
			}
			.keyboard ul li.folded img{
				padding-left: 20%;
				padding-top: 5%;
				width: 60%;
				height: 80%;
			}
			
		.keyboard ul li.del{
			background:#d4d8db ;
			background-size:auto 50%;
			border-bottom:0
			}
		.keyboard ul li.confirm{
			background:#0c97ea !important;
			height:75%;
			float:right;
			border-left:0
			}
		.keyboard ul li.confirm:after{
			color:#fff;
			content:attr(data-txt)}
		.keyboard ul li.confirm:active{
				background-color:#137f13
				}
		.keyboard ul li:active{
			background-color:#efefef
			}
			.bottomImg{
				/*text-align: center;*/
			}
			.bottomImg img{
				position: fixed;
				bottom: 10px;
				margin-left: -15%;
				/*margin-top: 65%;*/
				/*z-index: -1;*/
				left: 50%;
				
			}
				.main{
				height: 100% !important;
			}
			
    	</style>

@endsection



@section('content')
  <body ontouchstart>
    <div class="main">
        <p class="cite">
		<span>
			<img src="{{url('/img/site.jpg')}}">
		</span>{{$shop['auth_shop_name']}}
        </p>
        <div class="type">
        	
        	
            <!--<div class="top clear">
                <span>消费总金额（元）</span>
                <input id="total_amount" value="" type="number" placeholder="请询问服务员后输入">
            </div>
            -->
             <div class="ipt" >
        <span>消费金额</span><i></i>
        
        <div class="price">¥<span id="price" style="font-size: 30px;"></span></div>

    </div>
            
            
            {{--<div class="bot clear">--}}
                {{--<span class="no">不参与优惠金额（元）</span>--}}
                {{--<input type="number" placeholder="请询问服务员后输入" id="ask">--}}
            {{--</div>--}}
        </div>
        <div class="type type_bot">
            <div class="bot clear">
                <span>选填备注</span>
                <input type="text" placeholder="如包房号、服务员号等" class="notice" name="remark" id="remark">
            </div>
        </div>
        
        
        {{--   <p class="sale">商家优惠</p>
           <p class="down">8.5折</p>--}}
        <!--<button type="button" id="payLogButton" class="btn db" style="font-size: 18px;">和店员已确认，立即买单</button>-->
        <input type="hidden" value="{{$shop['store_id']}}" id="store_id">
        <input type="hidden" value="{{$m_id}}" id="m_id">
        <input type="hidden" id="token" value="{{csrf_token()}}">
        	
        	<div class="bottomImg">
        		<img src="{{url('/img/zhifubaozhi.png')}}"/>
        	</div>
        	</div>
  <div class="keyboard">
    <ul>
        <li data-num="1"></li>
        <li data-num="2"></li>
        <li data-num="3"></li>
        <li class="del"><img src="{{url('/img/icon_del.png')}}"></li>
        <li class="confirm" data-txt="确认"  id="payLogButton">
			{{--<button  id="payLogButton"  style="width: 100%;height: 100%;background-color: transparent;border: none;outline: medium"></button>--}}
		</li>
        <li data-num="4"></li>
        <li data-num="5"></li>
        <li data-num="6"></li>
        <li data-num="7"></li>
        <li data-num="8"></li>
        <li data-num="9"></li>
        <li class="folded"><img src="{{url('/img/icon_folded.png')}}"></li>
        <li data-num="0"></li>
        <li data-num="."></li>
    </ul>
</div>

 </body>
        @endsection
        @section('js')
            <script>
                document.addEventListener('AlipayJSBridgeReady', function () {
					var ck=1;
                    $("#payLogButton").click(function () {
						if(ck&&$("#price").html()){
						    ck=0;
                            $.post("{{route('AlipayOqrCreate')}}", {
                                total_amount: $("#price").html(),
                                store_id: $("#store_id").val(),
                                remark:$("#remark").val(),
                                _token: $("#token").val(),
                                m_id:$("#m_id").val(),
                            }, function (data) {
                                ck=1;
                                if (data.status == 1) {
                                    AlipayJSBridge.call("tradePay", {
                                        tradeNO: data.trade_no
                                    }, function (result) {
                                        //付款成功
                                        if (result.resultCode == "9000") {
                                            window.location.href = "{{url('admin/alipayopen/PaySuccess?price=')}}" + $("#price").html()+"&out_trade_no="+data.out_trade_no;
                                        }
                                        if (result.resultCode == "6001") {
                                            window.location.href = "{{url('admin/alipayopen/OrderErrors?code=6001')}}";
                                        }

										/*  //同步更新状态route('OrderStatus')
										 $.post("", {
										 trade_no: data.trade_no,
										 resultCode: result.resultCode,
										 _token: $("#token").val()
										 },
										 function (dataStatus) {
										 //付款成功
										 if (result.resultCode == "9000") {
										 }
										 if (result.resultCode == "6001") {
										 }
										 }, "json");*/

                                    });
                                } else {
                                    window.location.href = "{{url('admin/alipayopen/OrderErrors')}}";
                                }
                            }, "json");
						}

                    });
                }, false);
                
                
            </script>
            
            <script>
    $(function () {
        FastClick.attach(document.body);
    });
    $(function () {
        $("#isInvoice").change(function (event) {
            if ($(this).is(":checked")) {
                $("#invoiceCon").slideDown();
            } else {
                $("#invoiceCon").slideUp();
            }
        });
        $(".ipt").click(function (event) {
        	
            if ($(".keyboard").hasClass('unfolded')) {
                $(".keyboard").animate({"bottom": "0"}, 300, function () {
                    $(this).removeClass('unfolded');
                });
            }
        });
        
        $("#remark").focus(function(){
        	$(".keyboard").hide();
        })
        
         $("#remark").blur(function(){
        	$(".keyboard").show();
        	if ($(".keyboard").hasClass('unfolded')) {
                $(".keyboard").animate({"bottom": "0"}, 300, function () {
                    $(this).removeClass('unfolded');
                });
            }
        	
        	
        })
        
        
        
        
         $("#ask").focus(function(){
        	$(".keyboard").hide();
        })
        
         $("#ask").blur(function(){
        	$(".keyboard").show();
        	if ($(".keyboard").hasClass('unfolded')) {
                $(".keyboard").animate({"bottom": "0"}, 300, function () {
                    $(this).removeClass('unfolded');
                });
            }
        	
        	
        })
      
      
      
        

        var $money = $("#price");
        $(".keyboard li").on("touchend", function () {
            var _this = $(this);
            var num = _this.attr("data-num");
            var oldValue = $money.html();
            var newValue = "";
            if (_this.hasClass('del')) {
                newValue = oldValue.substring(0, oldValue.length - 1);
                if (newValue == "") {
                    newValue = "";
                }
                $money.html(newValue);
            } else if (_this.hasClass('folded')) {
                $(".keyboard").animate({"bottom": "-100%"}, 300, function () {
                    $(this).addClass('unfolded');
                });
            } else if (_this.hasClass('confirm')) {

                var price = $("#price").html();
                var remarks = $("#remarks").val();
                var fixed = $("#fixed").val();
                var qid = $("#qid").val();
                if (price == ''||price == '0' ||price == '0.') {
//                  $.toast("请输入消费金额", "text");
                    alert('请输入消费金额');
                    return false;
                }

//              var url = "/Home/Qrcode/fixed";
//              url += "?money="+price+"&remarks="+remarks+"&id="+qid;

                //提交数据到下个页面
//              window.location.href = url;

            } else {
                if (oldValue == "0") {
                    if (num != ".") {
                        oldValue = "";
                    }
                }
                if (oldValue == "") {
                    if (num == ".") {
                        oldValue = "0.";
                    }
                }
                newValue = oldValue + num;

                reg = /^\d{0,5}(\.\d{0,2})?$/g;

                if (reg.test(newValue)) {
                    $money.html(newValue);
                } else {
                    $money.html(oldValue);
                }
            }
        });

        $(".weui-input").focus(function (event) {
            $(".keyboard").hide();
        }).focusout(function (event) {
            $(".keyboard").show();
        });


    });
    
    
    
      document.documentElement.addEventListener('dblclick', function(e){
    e.preventDefault();
});

document.addEventListener('touchmove', function(event) {
        event.preventDefault();
    }, false);



</script>

<script type="text/javascript">
	(function () {
	'use strict';
	
	/**
	 * @preserve FastClick: polyfill to remove click delays on browsers with touch UIs.
	 *
	 * @version 1.0.3
	 * @codingstandard ftlabs-jsv2
	 * @copyright The Financial Times Limited [All Rights Reserved]
	 * @license MIT License (see LICENSE.txt)
	 */
	
	/*jslint browser:true, node:true*/
	/*global define, Event, Node*/
	
	
	/**
	 * Instantiate fast-clicking listeners on the specified layer.
	 *
	 * @constructor
	 * @param {Element} layer The layer to listen on
	 * @param {Object} options The options to override the defaults
	 */
	function FastClick(layer, options) {
		var oldOnClick;
	
		options = options || {};
	
		/**
		 * Whether a click is currently being tracked.
		 *
		 * @type boolean
		 */
		this.trackingClick = false;
	
	
		/**
		 * Timestamp for when click tracking started.
		 *
		 * @type number
		 */
		this.trackingClickStart = 0;
	
	
		/**
		 * The element being tracked for a click.
		 *
		 * @type EventTarget
		 */
		this.targetElement = null;
	
	
		/**
		 * X-coordinate of touch start event.
		 *
		 * @type number
		 */
		this.touchStartX = 0;
	
	
		/**
		 * Y-coordinate of touch start event.
		 *
		 * @type number
		 */
		this.touchStartY = 0;
	
	
		/**
		 * ID of the last touch, retrieved from Touch.identifier.
		 *
		 * @type number
		 */
		this.lastTouchIdentifier = 0;
	
	
		/**
		 * Touchmove boundary, beyond which a click will be cancelled.
		 *
		 * @type number
		 */
		this.touchBoundary = options.touchBoundary || 10;
	
	
		/**
		 * The FastClick layer.
		 *
		 * @type Element
		 */
		this.layer = layer;
	
		/**
		 * The minimum time between tap(touchstart and touchend) events
		 *
		 * @type number
		 */
		this.tapDelay = options.tapDelay || 200;
	
		if (FastClick.notNeeded(layer)) {
			return;
		}
	
		// Some old versions of Android don't have Function.prototype.bind
		function bind(method, context) {
			return function() { return method.apply(context, arguments); };
		}
	
	
		var methods = ['onMouse', 'onClick', 'onTouchStart', 'onTouchMove', 'onTouchEnd', 'onTouchCancel'];
		var context = this;
		for (var i = 0, l = methods.length; i < l; i++) {
			context[methods[i]] = bind(context[methods[i]], context);
		}
	
		// Set up event handlers as required
		if (deviceIsAndroid) {
			layer.addEventListener('mouseover', this.onMouse, true);
			layer.addEventListener('mousedown', this.onMouse, true);
			layer.addEventListener('mouseup', this.onMouse, true);
		}
	
		layer.addEventListener('click', this.onClick, true);
		layer.addEventListener('touchstart', this.onTouchStart, false);
		layer.addEventListener('touchmove', this.onTouchMove, false);
		layer.addEventListener('touchend', this.onTouchEnd, false);
		layer.addEventListener('touchcancel', this.onTouchCancel, false);
	
		// Hack is required for browsers that don't support Event#stopImmediatePropagation (e.g. Android 2)
		// which is how FastClick normally stops click events bubbling to callbacks registered on the FastClick
		// layer when they are cancelled.
		if (!Event.prototype.stopImmediatePropagation) {
			layer.removeEventListener = function(type, callback, capture) {
				var rmv = Node.prototype.removeEventListener;
				if (type === 'click') {
					rmv.call(layer, type, callback.hijacked || callback, capture);
				} else {
					rmv.call(layer, type, callback, capture);
				}
			};
	
			layer.addEventListener = function(type, callback, capture) {
				var adv = Node.prototype.addEventListener;
				if (type === 'click') {
					adv.call(layer, type, callback.hijacked || (callback.hijacked = function(event) {
						if (!event.propagationStopped) {
							callback(event);
						}
					}), capture);
				} else {
					adv.call(layer, type, callback, capture);
				}
			};
		}
	
		// If a handler is already declared in the element's onclick attribute, it will be fired before
		// FastClick's onClick handler. Fix this by pulling out the user-defined handler function and
		// adding it as listener.
		if (typeof layer.onclick === 'function') {
	
			// Android browser on at least 3.2 requires a new reference to the function in layer.onclick
			// - the old one won't work if passed to addEventListener directly.
			oldOnClick = layer.onclick;
			layer.addEventListener('click', function(event) {
				oldOnClick(event);
			}, false);
			layer.onclick = null;
		}
	}
	
	
	/**
	 * Android requires exceptions.
	 *
	 * @type boolean
	 */
	var deviceIsAndroid = navigator.userAgent.indexOf('Android') > 0;
	
	
	/**
	 * iOS requires exceptions.
	 *
	 * @type boolean
	 */
	var deviceIsIOS = /iP(ad|hone|od)/.test(navigator.userAgent);
	
	
	/**
	 * iOS 4 requires an exception for select elements.
	 *
	 * @type boolean
	 */
	var deviceIsIOS4 = deviceIsIOS && (/OS 4_\d(_\d)?/).test(navigator.userAgent);
	
	
	/**
	 * iOS 6.0(+?) requires the target element to be manually derived
	 *
	 * @type boolean
	 */
	var deviceIsIOSWithBadTarget = deviceIsIOS && (/OS ([6-9]|\d{2})_\d/).test(navigator.userAgent);
	
	/**
	 * BlackBerry requires exceptions.
	 *
	 * @type boolean
	 */
	var deviceIsBlackBerry10 = navigator.userAgent.indexOf('BB10') > 0;
	
	/**
	 * Determine whether a given element requires a native click.
	 *
	 * @param {EventTarget|Element} target Target DOM element
	 * @returns {boolean} Returns true if the element needs a native click
	 */
	FastClick.prototype.needsClick = function(target) {
		switch (target.nodeName.toLowerCase()) {
	
		// Don't send a synthetic click to disabled inputs (issue #62)
		case 'button':
		case 'select':
		case 'textarea':
			if (target.disabled) {
				return true;
			}
	
			break;
		case 'input':
	
			// File inputs need real clicks on iOS 6 due to a browser bug (issue #68)
			if ((deviceIsIOS && target.type === 'file') || target.disabled) {
				return true;
			}
	
			break;
		case 'label':
		case 'video':
			return true;
		}
	
		return (/\bneedsclick\b/).test(target.className);
	};
	
	
	/**
	 * Determine whether a given element requires a call to focus to simulate click into element.
	 *
	 * @param {EventTarget|Element} target Target DOM element
	 * @returns {boolean} Returns true if the element requires a call to focus to simulate native click.
	 */
	FastClick.prototype.needsFocus = function(target) {
		switch (target.nodeName.toLowerCase()) {
		case 'textarea':
			return true;
		case 'select':
			return !deviceIsAndroid;
		case 'input':
			switch (target.type) {
			case 'button':
			case 'checkbox':
			case 'file':
			case 'image':
			case 'radio':
			case 'submit':
				return false;
			}
	
			// No point in attempting to focus disabled inputs
			return !target.disabled && !target.readOnly;
		default:
			return (/\bneedsfocus\b/).test(target.className);
		}
	};
	
	
	/**
	 * Send a click event to the specified element.
	 *
	 * @param {EventTarget|Element} targetElement
	 * @param {Event} event
	 */
	FastClick.prototype.sendClick = function(targetElement, event) {
		var clickEvent, touch;
	
		// On some Android devices activeElement needs to be blurred otherwise the synthetic click will have no effect (#24)
		if (document.activeElement && document.activeElement !== targetElement) {
			document.activeElement.blur();
		}
	
		touch = event.changedTouches[0];
	
		// Synthesise a click event, with an extra attribute so it can be tracked
		clickEvent = document.createEvent('MouseEvents');
		clickEvent.initMouseEvent(this.determineEventType(targetElement), true, true, window, 1, touch.screenX, touch.screenY, touch.clientX, touch.clientY, false, false, false, false, 0, null);
		clickEvent.forwardedTouchEvent = true;
		targetElement.dispatchEvent(clickEvent);
	};
	
	FastClick.prototype.determineEventType = function(targetElement) {
	
		//Issue #159: Android Chrome Select Box does not open with a synthetic click event
		if (deviceIsAndroid && targetElement.tagName.toLowerCase() === 'select') {
			return 'mousedown';
		}
	
		return 'click';
	};
	
	
	/**
	 * @param {EventTarget|Element} targetElement
	 */
	FastClick.prototype.focus = function(targetElement) {
		var length;
	
		// Issue #160: on iOS 7, some input elements (e.g. date datetime month) throw a vague TypeError on setSelectionRange. These elements don't have an integer value for the selectionStart and selectionEnd properties, but unfortunately that can't be used for detection because accessing the properties also throws a TypeError. Just check the type instead. Filed as Apple bug #15122724.
		if (deviceIsIOS && targetElement.setSelectionRange && targetElement.type.indexOf('date') !== 0 && targetElement.type !== 'time' && targetElement.type !== 'month') {
			length = targetElement.value.length;
			targetElement.setSelectionRange(length, length);
		} else {
			targetElement.focus();
		}
	};
	
	
	/**
	 * Check whether the given target element is a child of a scrollable layer and if so, set a flag on it.
	 *
	 * @param {EventTarget|Element} targetElement
	 */
	FastClick.prototype.updateScrollParent = function(targetElement) {
		var scrollParent, parentElement;
	
		scrollParent = targetElement.fastClickScrollParent;
	
		// Attempt to discover whether the target element is contained within a scrollable layer. Re-check if the
		// target element was moved to another parent.
		if (!scrollParent || !scrollParent.contains(targetElement)) {
			parentElement = targetElement;
			do {
				if (parentElement.scrollHeight > parentElement.offsetHeight) {
					scrollParent = parentElement;
					targetElement.fastClickScrollParent = parentElement;
					break;
				}
	
				parentElement = parentElement.parentElement;
			} while (parentElement);
		}
	
		// Always update the scroll top tracker if possible.
		if (scrollParent) {
			scrollParent.fastClickLastScrollTop = scrollParent.scrollTop;
		}
	};
	
	
	/**
	 * @param {EventTarget} targetElement
	 * @returns {Element|EventTarget}
	 */
	FastClick.prototype.getTargetElementFromEventTarget = function(eventTarget) {
	
		// On some older browsers (notably Safari on iOS 4.1 - see issue #56) the event target may be a text node.
		if (eventTarget.nodeType === Node.TEXT_NODE) {
			return eventTarget.parentNode;
		}
	
		return eventTarget;
	};
	
	
	/**
	 * On touch start, record the position and scroll offset.
	 *
	 * @param {Event} event
	 * @returns {boolean}
	 */
	FastClick.prototype.onTouchStart = function(event) {
		var targetElement, touch, selection;
	
		// Ignore multiple touches, otherwise pinch-to-zoom is prevented if both fingers are on the FastClick element (issue #111).
		if (event.targetTouches.length > 1) {
			return true;
		}
	
		targetElement = this.getTargetElementFromEventTarget(event.target);
		touch = event.targetTouches[0];
	
		if (deviceIsIOS) {
	
			// Only trusted events will deselect text on iOS (issue #49)
			selection = window.getSelection();
			if (selection.rangeCount && !selection.isCollapsed) {
				return true;
			}
	
			if (!deviceIsIOS4) {
	
				// Weird things happen on iOS when an alert or confirm dialog is opened from a click event callback (issue #23):
				// when the user next taps anywhere else on the page, new touchstart and touchend events are dispatched
				// with the same identifier as the touch event that previously triggered the click that triggered the alert.
				// Sadly, there is an issue on iOS 4 that causes some normal touch events to have the same identifier as an
				// immediately preceeding touch event (issue #52), so this fix is unavailable on that platform.
				// Issue 120: touch.identifier is 0 when Chrome dev tools 'Emulate touch events' is set with an iOS device UA string,
				// which causes all touch events to be ignored. As this block only applies to iOS, and iOS identifiers are always long,
				// random integers, it's safe to to continue if the identifier is 0 here.
				if (touch.identifier && touch.identifier === this.lastTouchIdentifier) {
					event.preventDefault();
					return false;
				}
	
				this.lastTouchIdentifier = touch.identifier;
	
				// If the target element is a child of a scrollable layer (using -webkit-overflow-scrolling: touch) and:
				// 1) the user does a fling scroll on the scrollable layer
				// 2) the user stops the fling scroll with another tap
				// then the event.target of the last 'touchend' event will be the element that was under the user's finger
				// when the fling scroll was started, causing FastClick to send a click event to that layer - unless a check
				// is made to ensure that a parent layer was not scrolled before sending a synthetic click (issue #42).
				this.updateScrollParent(targetElement);
			}
		}
	
		this.trackingClick = true;
		this.trackingClickStart = event.timeStamp;
		this.targetElement = targetElement;
	
		this.touchStartX = touch.pageX;
		this.touchStartY = touch.pageY;
	
		// Prevent phantom clicks on fast double-tap (issue #36)
		if ((event.timeStamp - this.lastClickTime) < this.tapDelay) {
			event.preventDefault();
		}
	
		return true;
	};
	
	
	/**
	 * Based on a touchmove event object, check whether the touch has moved past a boundary since it started.
	 *
	 * @param {Event} event
	 * @returns {boolean}
	 */
	FastClick.prototype.touchHasMoved = function(event) {
		var touch = event.changedTouches[0], boundary = this.touchBoundary;
	
		if (Math.abs(touch.pageX - this.touchStartX) > boundary || Math.abs(touch.pageY - this.touchStartY) > boundary) {
			return true;
		}
	
		return false;
	};
	
	
	/**
	 * Update the last position.
	 *
	 * @param {Event} event
	 * @returns {boolean}
	 */
	FastClick.prototype.onTouchMove = function(event) {
		if (!this.trackingClick) {
			return true;
		}
	
		// If the touch has moved, cancel the click tracking
		if (this.targetElement !== this.getTargetElementFromEventTarget(event.target) || this.touchHasMoved(event)) {
			this.trackingClick = false;
			this.targetElement = null;
		}
	
		return true;
	};
	
	
	/**
	 * Attempt to find the labelled control for the given label element.
	 *
	 * @param {EventTarget|HTMLLabelElement} labelElement
	 * @returns {Element|null}
	 */
	FastClick.prototype.findControl = function(labelElement) {
	
		// Fast path for newer browsers supporting the HTML5 control attribute
		if (labelElement.control !== undefined) {
			return labelElement.control;
		}
	
		// All browsers under test that support touch events also support the HTML5 htmlFor attribute
		if (labelElement.htmlFor) {
			return document.getElementById(labelElement.htmlFor);
		}
	
		// If no for attribute exists, attempt to retrieve the first labellable descendant element
		// the list of which is defined here: http://www.w3.org/TR/html5/forms.html#category-label
		return labelElement.querySelector('button, input:not([type=hidden]), keygen, meter, output, progress, select, textarea');
	};
	
	
	/**
	 * On touch end, determine whether to send a click event at once.
	 *
	 * @param {Event} event
	 * @returns {boolean}
	 */
	FastClick.prototype.onTouchEnd = function(event) {
		var forElement, trackingClickStart, targetTagName, scrollParent, touch, targetElement = this.targetElement;
	
		if (!this.trackingClick) {
			return true;
		}
	
		// Prevent phantom clicks on fast double-tap (issue #36)
		if ((event.timeStamp - this.lastClickTime) < this.tapDelay) {
			this.cancelNextClick = true;
			return true;
		}
	
		// Reset to prevent wrong click cancel on input (issue #156).
		this.cancelNextClick = false;
	
		this.lastClickTime = event.timeStamp;
	
		trackingClickStart = this.trackingClickStart;
		this.trackingClick = false;
		this.trackingClickStart = 0;
	
		// On some iOS devices, the targetElement supplied with the event is invalid if the layer
		// is performing a transition or scroll, and has to be re-detected manually. Note that
		// for this to function correctly, it must be called *after* the event target is checked!
		// See issue #57; also filed as rdar://13048589 .
		if (deviceIsIOSWithBadTarget) {
			touch = event.changedTouches[0];
	
			// In certain cases arguments of elementFromPoint can be negative, so prevent setting targetElement to null
			targetElement = document.elementFromPoint(touch.pageX - window.pageXOffset, touch.pageY - window.pageYOffset) || targetElement;
			targetElement.fastClickScrollParent = this.targetElement.fastClickScrollParent;
		}
	
		targetTagName = targetElement.tagName.toLowerCase();
		if (targetTagName === 'label') {
			forElement = this.findControl(targetElement);
			if (forElement) {
				this.focus(targetElement);
				if (deviceIsAndroid) {
					return false;
				}
	
				targetElement = forElement;
			}
		} else if (this.needsFocus(targetElement)) {
	
			// Case 1: If the touch started a while ago (best guess is 100ms based on tests for issue #36) then focus will be triggered anyway. Return early and unset the target element reference so that the subsequent click will be allowed through.
			// Case 2: Without this exception for input elements tapped when the document is contained in an iframe, then any inputted text won't be visible even though the value attribute is updated as the user types (issue #37).
			if ((event.timeStamp - trackingClickStart) > 100 || (deviceIsIOS && window.top !== window && targetTagName === 'input')) {
				this.targetElement = null;
				return false;
			}
	
			this.focus(targetElement);
			this.sendClick(targetElement, event);
	
			// Select elements need the event to go through on iOS 4, otherwise the selector menu won't open.
			// Also this breaks opening selects when VoiceOver is active on iOS6, iOS7 (and possibly others)
			if (!deviceIsIOS || targetTagName !== 'select') {
				this.targetElement = null;
				event.preventDefault();
			}
	
			return false;
		}
	
		if (deviceIsIOS && !deviceIsIOS4) {
	
			// Don't send a synthetic click event if the target element is contained within a parent layer that was scrolled
			// and this tap is being used to stop the scrolling (usually initiated by a fling - issue #42).
			scrollParent = targetElement.fastClickScrollParent;
			if (scrollParent && scrollParent.fastClickLastScrollTop !== scrollParent.scrollTop) {
				return true;
			}
		}
	
		// Prevent the actual click from going though - unless the target node is marked as requiring
		// real clicks or if it is in the whitelist in which case only non-programmatic clicks are permitted.
		if (!this.needsClick(targetElement)) {
			event.preventDefault();
			this.sendClick(targetElement, event);
		}
	
		return false;
	};
	
	
	/**
	 * On touch cancel, stop tracking the click.
	 *
	 * @returns {void}
	 */
	FastClick.prototype.onTouchCancel = function() {
		this.trackingClick = false;
		this.targetElement = null;
	};
	
	
	/**
	 * Determine mouse events which should be permitted.
	 *
	 * @param {Event} event
	 * @returns {boolean}
	 */
	FastClick.prototype.onMouse = function(event) {
	
		// If a target element was never set (because a touch event was never fired) allow the event
		if (!this.targetElement) {
			return true;
		}
	
		if (event.forwardedTouchEvent) {
			return true;
		}
	
		// Programmatically generated events targeting a specific element should be permitted
		if (!event.cancelable) {
			return true;
		}
	
		// Derive and check the target element to see whether the mouse event needs to be permitted;
		// unless explicitly enabled, prevent non-touch click events from triggering actions,
		// to prevent ghost/doubleclicks.
		if (!this.needsClick(this.targetElement) || this.cancelNextClick) {
	
			// Prevent any user-added listeners declared on FastClick element from being fired.
			if (event.stopImmediatePropagation) {
				event.stopImmediatePropagation();
			} else {
	
				// Part of the hack for browsers that don't support Event#stopImmediatePropagation (e.g. Android 2)
				event.propagationStopped = true;
			}
	
			// Cancel the event
			event.stopPropagation();
			event.preventDefault();
	
			return false;
		}
	
		// If the mouse event is permitted, return true for the action to go through.
		return true;
	};
	
	
	/**
	 * On actual clicks, determine whether this is a touch-generated click, a click action occurring
	 * naturally after a delay after a touch (which needs to be cancelled to avoid duplication), or
	 * an actual click which should be permitted.
	 *
	 * @param {Event} event
	 * @returns {boolean}
	 */
	FastClick.prototype.onClick = function(event) {
		var permitted;
	
		// It's possible for another FastClick-like library delivered with third-party code to fire a click event before FastClick does (issue #44). In that case, set the click-tracking flag back to false and return early. This will cause onTouchEnd to return early.
		if (this.trackingClick) {
			this.targetElement = null;
			this.trackingClick = false;
			return true;
		}
	
		// Very odd behaviour on iOS (issue #18): if a submit element is present inside a form and the user hits enter in the iOS simulator or clicks the Go button on the pop-up OS keyboard the a kind of 'fake' click event will be triggered with the submit-type input element as the target.
		if (event.target.type === 'submit' && event.detail === 0) {
			return true;
		}
	
		permitted = this.onMouse(event);
	
		// Only unset targetElement if the click is not permitted. This will ensure that the check for !targetElement in onMouse fails and the browser's click doesn't go through.
		if (!permitted) {
			this.targetElement = null;
		}
	
		// If clicks are permitted, return true for the action to go through.
		return permitted;
	};
	
	
	/**
	 * Remove all FastClick's event listeners.
	 *
	 * @returns {void}
	 */
	FastClick.prototype.destroy = function() {
		var layer = this.layer;
	
		if (deviceIsAndroid) {
			layer.removeEventListener('mouseover', this.onMouse, true);
			layer.removeEventListener('mousedown', this.onMouse, true);
			layer.removeEventListener('mouseup', this.onMouse, true);
		}
	
		layer.removeEventListener('click', this.onClick, true);
		layer.removeEventListener('touchstart', this.onTouchStart, false);
		layer.removeEventListener('touchmove', this.onTouchMove, false);
		layer.removeEventListener('touchend', this.onTouchEnd, false);
		layer.removeEventListener('touchcancel', this.onTouchCancel, false);
	};
	
	
	/**
	 * Check whether FastClick is needed.
	 *
	 * @param {Element} layer The layer to listen on
	 */
	FastClick.notNeeded = function(layer) {
		var metaViewport;
		var chromeVersion;
		var blackberryVersion;
	
		// Devices that don't support touch don't need FastClick
		if (typeof window.ontouchstart === 'undefined') {
			return true;
		}
	
		// Chrome version - zero for other browsers
		chromeVersion = +(/Chrome\/([0-9]+)/.exec(navigator.userAgent) || [,0])[1];
	
		if (chromeVersion) {
	
			if (deviceIsAndroid) {
				metaViewport = document.querySelector('meta[name=viewport]');
	
				if (metaViewport) {
					// Chrome on Android with user-scalable="no" doesn't need FastClick (issue #89)
					if (metaViewport.content.indexOf('user-scalable=no') !== -1) {
						return true;
					}
					// Chrome 32 and above with width=device-width or less don't need FastClick
					if (chromeVersion > 31 && document.documentElement.scrollWidth <= window.outerWidth) {
						return true;
					}
				}
	
			// Chrome desktop doesn't need FastClick (issue #15)
			} else {
				return true;
			}
		}
	
		if (deviceIsBlackBerry10) {
			blackberryVersion = navigator.userAgent.match(/Version\/([0-9]*)\.([0-9]*)/);
	
			// BlackBerry 10.3+ does not require Fastclick library.
			// https://github.com/ftlabs/fastclick/issues/251
			if (blackberryVersion[1] >= 10 && blackberryVersion[2] >= 3) {
				metaViewport = document.querySelector('meta[name=viewport]');
	
				if (metaViewport) {
					// user-scalable=no eliminates click delay.
					if (metaViewport.content.indexOf('user-scalable=no') !== -1) {
						return true;
					}
					// width=device-width (or less than device-width) eliminates click delay.
					if (document.documentElement.scrollWidth <= window.outerWidth) {
						return true;
					}
				}
			}
		}
	
		// IE10 with -ms-touch-action: none, which disables double-tap-to-zoom (issue #97)
		if (layer.style.msTouchAction === 'none') {
			return true;
		}
	
		return false;
	};
	
	
	/**
	 * Factory method for creating a FastClick object
	 *
	 * @param {Element} layer The layer to listen on
	 * @param {Object} options The options to override the defaults
	 */
	FastClick.attach = function(layer, options) {
		return new FastClick(layer, options);
	};
	
	
	if (typeof define == 'function' && typeof define.amd == 'object' && define.amd) {
	
		// AMD. Register as an anonymous module.
		define(function() {
			return FastClick;
		});
	} else if (typeof module !== 'undefined' && module.exports) {
		module.exports = FastClick.attach;
		module.exports.FastClick = FastClick;
	} else {
		window.FastClick = FastClick;
	}
}());
</script>


            
@endsection



