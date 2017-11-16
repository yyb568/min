<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">登录账号：</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="uname" id="uname" value="<?=$info['uname']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">手机号：</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="phone" id="phone" value="<?=$info['phone']?>">
                                </div>
                            </div>
                           <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">登录密码：</label>
                                <div class="col-sm-4">
                                    <input type="password" class="form-control" name="password" id="password" value="<?=$info['password']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        	<div class="form-group">
                                <label class="col-sm-2 control-label">性别：</label>
                                <div class="col-sm-4">
                                	<div class="radio">
                                        <label>
                                            <input type="radio"  value="1" id="sex" name="sex" <?php if ($info['sex'] == 1){echo 'checked';}?>>男</label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="2" id="sex" name="sex" <?php if ($info['sex'] == 2){echo 'checked';}?>>女</label>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">微信昵称：</label>
                                <div class="col-sm-4">
                                	<input type="text" class="form-control" name="nick" id="nick" value="<?=$info['nick']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态：</label>
                                <div class="col-sm-2">
                                	<div class="radio">
                                        <label>
                                            <input type="radio" value="1" id="sex" name="status" <?php if ($info['status'] == 1){echo 'checked';}?>>启用</label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="0" id="sex" name="status" <?php if ($info['status'] == 0){echo 'checked';}?>>冻结</label>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="button" onClick="doSubmit();">保存信息</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view("main/footer");?>
<script>
//保存配置信息
function doSubmit(){
	$.post("<?=site_url("user/doSave/{$info['id']}")?>",$("#form1").serialize(),function(data){
		if (data.status == 0){
			showTips('保存成功！','success','',1);
			setTimeout(function(){
				window.parent.location.reload();
			},1000);
		}else{
			showTips(data.info,'','',1);
		}
	},'json');
}
</script>