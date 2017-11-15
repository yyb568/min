<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">分组名称</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="group_name" id="group_name" value="<?=$info['group_name']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否启用</label>
                                <div class="col-sm-2">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="1" id="isshow" name="isshow" <?php if ($info['isshow'] == 1){ ?>checked<?php } ?>>是</label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="0" id="isshow" name="isshow" <?php if ($info['isshow'] == 0){ ?>checked<?php } ?>>否</label>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="button" onClick="doSubmit();">保存内容</button>
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
	$.post("<?=site_url("auth/doGroupSave/{$info['id']}")?>",$("#form1").serialize(),function(data){
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