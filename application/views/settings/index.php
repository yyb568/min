<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>系统设置</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">系统名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="webname" id="webname" value="<?=$info['webname']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">网站地址</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="http://" value="<?=$info['weburl']?>" name="weburl" id="weburl">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">网站域名</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="domain" id="domain" value="<?=$info['domain']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否开放注册</label>

                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="1" id="isregister" name="isregister" <?php if ($info['isregister'] == 1){ ?>checked<?php } ?>>是</label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="0" id="isregister" name="isregister" <?php if ($info['isregister'] == 0){ ?>checked<?php } ?>>否</label>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">系统邮箱</label>

                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" name="email" id="email" value="<?=$info['email']?>">
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
	$.post("<?=site_url("settings/doSave/{$infoid}")?>",$("#form1").serialize(),function(data){
		if (data.status == 0){
			showTips('保存成功！','success');
			setTimeout(function(){
				window.location.reload();
			},1000);
		}else{
			showTips(data.info);
		}
	},'json');
}
</script>