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
$_provinceList = $_districtList = $_cityList = array();
foreach((array)$pro as $key => $val){
	$_provinceList[$val['ProvinceID']] = $val;
}
foreach((array)$dis as $key => $val){
	$_districtList[$val['DistrictID']] = $val;
}
foreach((array)$city as $key => $val){
	$_cityList[$val['CityID']] = $val;
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

//选择地区
function switchVoid(){	   
	var area = $("#area").val();
	showFarme('选择地区','<?=site_url("area/switch_list/?userType=1&area=")?>'+area,'50%','80%');
}
/**
 * 地区选择框回调获取地址
 */
function parentSwitchArea(pro,cty,dis){
	province = pro;
	city = cty;
	district = dis;
	$("#area").val(province+","+city+","+district);
	$("#area_txt").html("已选择").css("color","green");
	if ($('input[name="finance"]:checked').val() == 2){		//只有选择地区管理员的时候才进行机型获取
		$.get("<?=site_url("auth/getMachList")?>?pro="+province+"&city="+city+"&district="+district,function(data){
			if (data.status == 1){
				showTips(data.info,'error',1);
			}else{
				var html = '<select class="form-control" multiple="" id="mach_ids" name="mach_ids[]">';
				$.each(data.info,function(i,j){
	                html += '<option value="'+data.info[i]['id']+'">'+data.info[i]['mach_type']+'</option>';
				});
				html += '</select>';
				$("#mach_lists").html(html);
				$("#mach_ids").show();
			}
		},'json');
	}
}
</script>