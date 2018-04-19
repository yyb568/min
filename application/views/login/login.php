<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache" />
    <title>智能选股系统</title>
    <link href="<?=static_url("css")?>bootstrap.min.css" rel="stylesheet">
    <link href="<?=static_url("css")?>font-awesome.min.css"  rel="stylesheet">
    <link href="<?=static_url("css")?>animate.min.css"  rel="stylesheet">
    <link href="<?=static_url("css")?>style.min.css"  rel="stylesheet">
    <link href="<?=static_url("css") ?>/plugins/toastr/toastr.min.css" rel="stylesheet">
    <link href="<?=static_url("js") ?>/plugins//layer/skin/layer.css" rel="stylesheet">
    <script>if(window.top !== window.self){ window.top.location = window.location;}</script>
</head>

<body class="gray-bg">

    <div class="middle-box text-center loginscreen  animated fadeInDown">
        <div>
            <div>

                <h1 class="logo-name">策略</h1>

            </div>
            <h3>智能选股系统</h3>
			<p>
            <form class="m-t" role="form">
                <div class="form-group">
                    <input type="text" name="username" id="username" class="form-control" placeholder="用户名" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="密码" required="">
                </div>
                <button type="button" class="btn btn-primary block full-width m-b" onClick="doSubmit();">登 录</button>
            </form>
        </div>
    </div>
    
    <script src="<?=static_url("js")?>jquery.min.js" ></script>
    <script src="<?=static_url("js")?>bootstrap.min.js" ></script>
    <script src="<?=static_url("js") ?>/plugins/toastr/toastr.min.js"></script>
    <script src="<?=static_url("js")?>common.js" ></script>
</body>
<script>
function doSubmit(){
	var username = $("#username").val()
	var password = $("#password").val();
	if (!username || !password){showTips('请填写账号密码再登陆！','error');return false;}
	$.post("<?=site_url("login/isLogin")?>",{"username":username,password:password},function(data){
		if (data.status == 1){
			showTips(data.info,'error');
		}else{
			window.location.href = '<?=site_url("home/index")?>';
		}
	},'json');
}
</script>
</html>