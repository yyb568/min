<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">权限名称</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="group_name" id="group_name" value="<?=$info['group_name']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">所属菜单</label>
                                <div class="col-sm-2">
                                    <select class="form-control m-b" name="Pmenu" id="Pmenu" onChange="getLastMenu(this.value);">
                                    	<option>请选择菜单</option>
                                    <?php foreach((array) $menuList as $key => $val){ ?>
                                        <option value="<?=$val['id']?>"><?=$val['menu_name']?></option>
                                    <?php } ?>
                                    </select>
                                    <div id="menu_lastd"></div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">控制器</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="group_name" id="group_name" value="<?=$info['group_name']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">动作</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="group_name" id="group_name" value="<?=$info['group_name']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">参数</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="group_name" id="group_name" value="<?=$info['group_name']?>">
                                </div>
                            </div>
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

//获取二级菜单
function getLastMenu(menu_pid){
	if (!menu_pid){showTips('请重新选择！','error');return false;}
	$.get('<?=site_url("auth/acl_menu")?>/'+menu_pid,function(data){
		if (data.info.length > 0){
			var html = '<select class="form-control m-b" name="menu_last" id="menu_last">';
			$.each(data.info,function(i,value){
				html += '<option value="'+value['id']+'">'+value['menu_name']+'</option>';
			});
			html += '</select>';
			$("#menu_lastd",self.document).html(html);
		}
	},'json');
}
</script>