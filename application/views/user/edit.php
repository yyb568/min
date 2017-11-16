<?php $this->load->view("main/header"); ?>
<?php
	$_provinceList = $_districtList = $_cityList = array();
	foreach($provinceList as $key => $val){
		$_provinceList[$val['ProvinceID']] = $val;
	}
	foreach($districtList as $key => $val){
		$_districtList[$val['DistrictID']] = $val;
	}
	foreach($cityList as $key => $val){
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
	                        <div class="hr-line-dashed"></div>
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">姓名：</label>
	                                <div class="col-sm-2">
	                                    <input type="text" class="form-control" name="uname" id="uname" value="<?=$info['uname']?>">
	                                </div>
	                            </div>
                        	<div class="form-group">
                                <label class="col-sm-2 control-label">性别：</label>
                                <div class="col-sm-2">
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
                                <label class="col-sm-2 control-label">手机号：</label>
                                <div class="col-sm-2">
                                	<input type="text" class="form-control" name="phone" id="phone" value="<?=$info['phone']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">发展人编码：</label>
                                <div class="col-sm-2">
                                	<input type="text" class="form-control" name="unicode" id="unicode" value="<?=$info['unicode']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">昵称：</label>
                                <div class="col-sm-2">
                                	<input type="text" class="form-control" name="nick" id="nick" value="<?=$info['nick']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">QQ：</label>
                                <div class="col-sm-2">
                                	<input type="text" class="form-control" name="qq" id="qq" value="<?=$info['qq']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">邮箱：</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control" name="email" id="email" value="<?=$info['email']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">身份证号：</label>
                                <div class="col-sm-4">
                                	 <input type="text" class="form-control" name="uid" id="uid" value="<?=$info['uid']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地区：</label>
                                <div class="col-sm-2">
                                <button data-toggle="button" class="btn btn-primary btn-outline" type="button" onClick="switchVoid();">选择地区</button>
	                                    <input type="hidden" name="area" id="area" value="<?=$info['province'] ?>,<?=$info['city']?>,<?=$info['district']?>">
	                                    <div id="area_txt">
                               			<?=$_provinceList[$info['province']]['ProvinceName'] ?> - <?=$_districtList[$info['district']]['DistrictName'] ?> - <?=$_cityList[$info['city']]['CityName'] ?>
                                		</div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">详细地址：</label>
                                <div class="col-sm-8">
                                	<input type="text" class="form-control" name="address" id="address" value="<?=$info['address']?>">
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
	var uid = $("#uid").val();
	var reg = /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/;
	if (reg.test(uid) == false){
		showTips('您输入的身份证号不正确！','error');
		return false;
	}
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

//选择地区
function switchVoid(){	   
	var area = $("#area").val();
	showFarme('选择地区','<?=site_url("area/switch_list/?userType=1&area=")?>'+area,'70%','90%');
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
}
</script>