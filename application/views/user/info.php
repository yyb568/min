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
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">会员ID：</label>
	                                <div class="col-sm-4">
	                                    <?=$info['id']?>
	                                </div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">姓名：</label>
	                                <div class="col-sm-4">
	                                    <?=$info['uname']?>
	                                </div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
                        	<div class="form-group">
                                <label class="col-sm-2 control-label">性别：</label>
                                <div class="col-sm-4">
                                <?php 
                                if ($info['sex'] == 1){
                                	echo '男';
                                }elseif ($info['sex'] == 2){
                                	echo '女';
                                }else{
                                	echo '未填写';
                                } ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">手机号：</label>
                                <div class="col-sm-4">
                                	<?=$info['phone']?>	
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">发展人编码：</label>
                                <div class="col-sm-4">
                                	<?=$info['unicode']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">昵称：</label>
                                <div class="col-sm-4">
                                	<?=$info['nick']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">QQ：</label>
                                <div class="col-sm-2">
                                	<?=$info['qq']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">邮箱：</label>
                                <div class="col-sm-2">
                                <?=$info['email']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">身份证号：</label>
                                <div class="col-sm-2">
                                	<?=$info['uid']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地区：</label>
                                <div class="col-sm-2">
                                <?=$_provinceList[$info['province']]['ProvinceName'] ?> - <?=$_cityList[$info['city']]['CityName'] ?> - <?=$_districtList[$info['district']]['DistrictName'] ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">详细地址：</label>
                                <div class="col-sm-2">
                                	<?=$info['address']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态：</label>
                                <div class="col-sm-2">
                                	<?php if ($info['status'] == 0){echo '<font color=red>冻结</font>';}else{echo '<font color=green>正常</font>';} ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">注册时间：</label>
                                <div class="col-sm-2">
                                <?=date("Y-m-d H:i:s",$info['created'])?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">最后登录时间：</label>
                                <div class="col-sm-2">
                                	<?=date("Y-m-d H:i:s",$info['last_time'])?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">资料最后更新时间：</label>
                                <div class="col-sm-2">
                                	<?=date("Y-m-d H:i:s",$info['last_update'])?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view("main/footer");?>
