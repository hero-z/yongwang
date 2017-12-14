<html>
<head>
    <meta charset='utf-8'/>
    <title></title>
    <style type="text/css">
        *{
            margin:0;
            padding:0;
        }
        a{
            text-decoration: none;
            color:#6699cc;
        }
        a:hover{
            cursor:pointer;
        }
        #main{
            width:100%;
            height:95%;
            position:absolute;
        }
        #hold{
            width:100%;
            height:20%;
            /*border:1px solid yellow;*/
        }
        #kuang{
            border:4px solid #ccc;
            width:90%;
            height:50%;
            border-radius: 25px;
            text-align: center;

            margin:0 auto;
            
        }
        #content{
            width:70%;
            height:100%;
            text-align: center;
            margin:0 auto;
            text-align: center;

            /*border:1px solid black;*/
            height:100%;
            text-align: left;
            color:#ccc;
            font-size:50px;
            font-weight:bold;
            font-family:"Times New Roman",Georgia,Serif;
        }
        #top{
            width:100%;
            height:40%;
            /*border:1px solid #fcd;*/
            position:relative;
        }
        #mid{
            width:100%;
            height:25%;
            /*border:1px solid #fca;*/

        }
        #bottom{
            width:100%;
            height:20%;
            /*border:1px solid #fcf;*/
            font-size:18px;
            text-align:right;
            color:black;
            font-weight: normal;
        }

        #top #pic{
            /*border:1px solid red;*/
            width:100px;
            height:100px;
            text-align:center;
            border-radius: 100px;
            color:white;
            background-color:red;
            font-size:70px;
            line-height:100px;
            font-family:"Times New Roman",Georgia,Serif;
            position:absolute;
            bottom:50px;
        }
 

    </style>
</head>

<body>
    <div id='main'>
        <div id='hold'></div>
        <div id='kuang'>
            <div id='content'>
                <div id="top">
                    <div id='pic'>!</div>
                </div>
                <div id="mid">
                        
                        {{$message}}
                </div>
                <div id="bottom">
                    等待  <span id='jump'><?= isset($time)&&$time ?></span> 秒后自动<a onclick='window.location.href=history.go(-1)'>跳转</a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                </div>
            </div>
        </div>

    </div>
</body>
</html>


<script type="text/javascript">
    var jump=document.getElementById('jump');

    var time=jump.innerHTML;
    if(!time)
    {
        time=3;
        jump.innerHTML=time;
    }
    function jumpf()
    {
        if(--time>0)
        {
            jump.innerHTML=time;
            setTimeout(jumpf,1000);
        }
        else
        {
            window.location.href=history.go(-1);
        }
    }

    setTimeout(jumpf,1000);

</script>
