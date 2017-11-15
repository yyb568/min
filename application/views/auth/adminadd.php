<?php $this->load->view("main/header"); ?>
<?php 
if ($info){
	if ($info['role'] == -1){
		$role = -1;
	}else{
		$role = unserialize($info['role']);
	}
}else{
	$role = array();
}
?>
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
                                    <input type="text" class="form-control" name="username" id="username" <?php if ($info){echo 'disabled';} ?> value="<?=$info['username']?>">
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
                                <label class="col-sm-2 control-label">姓名：</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="uname" id="uname" value="<?=$info['uname']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Email：</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="email" id="email" value="<?=$info['email']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">选择权限:</label>
                                <div class="col-sm-10">
                                <?php if ($role == -1){ ?>
                                <div class="checkbox"><label> <input name="role_list" id="role_list" type="checkbox" value="-1" <?php if ($role == -1){echo 'checked';} ?>>系统管理员</label></div>
                                <?php }else{ ?>
                                <?php foreach($menuList as $key => $val){ ?>
                                	<?php if ($val['pid'] != 0){ ?>
                                		<div class="checkbox"><label> <input <?php if (in_array($val['id'],$role)){echo 'checked';} ?> name="role_list[]" id="role_list" type="checkbox" value="<?=$val['id']?>"><?=$menuList[$val['pid']]['menu_name']?> -- ><?=$val['menu_name']?></label></div>
                                	<?php }else{ ?>
                                  		<div class="checkbox"><label> <input <?php if (in_array($val['id'],$role)){echo 'checked';} ?> name="role_list[]" id="role_list" type="checkbox" value="<?=$val['id']?>"><?=$val['menu_name']?></label></div>
                                  	<?php } ?>
                                <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="button" onClick="doSubmit();">创建账号</button>
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
	$.post("<?=site_url("auth/doAdminSave/{$info['id']}")?>",$("#form1").serialize(),function(data){
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