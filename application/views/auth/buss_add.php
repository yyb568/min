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
                                <label class="col-sm-2 control-label">平台商名称：</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="buss_name" id="buss_name" <?php if ($info){echo 'disabled';} ?> value="<?=$info['buss_name']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">选择结算方式：</label>
                                <div class="col-sm-4">
                                    <select name="bill_id" id="bill_id" class="form-control m-b">
                                    <option>请选择</option>
                                    <?php foreach($this->config->config['bill'] as $key => $val){ if (empty($val)){continue;}?>
                                    	<option value="<?=$key?>" <?php if ($info['bill'] == $key){echo 'selected';} ?>><?=$val?></option>
                                    <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">选择激励方式：</label>
                                <div class="col-sm-4">
                                    <select name="mech_id" id="mech_id" class="form-control m-b">
                                    <option>请选择</option>
                                    <?php foreach($this->config->config['mech'] as $key => $val){ if (empty($val)){continue;}?>
                                    	<option value="<?=$key?>" <?php if ($info['mech'] == $key){echo 'selected';} ?>><?=$val?></option>
                                    <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否启用</label>
                                <div class="col-sm-2">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="1" id="status" name="status" <?php if ($info['status'] == 1){ ?>checked<?php } ?>>是</label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="0" id="status" name="status" <?php if ($info['status'] == 0){ ?>checked<?php } ?>>否</label>
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
	$.post("<?=site_url("auth/doBussSave/{$info['id']}")?>",$("#form1").serialize(),function(data){
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