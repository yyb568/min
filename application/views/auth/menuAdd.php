<?php $this->load->view("main/header");?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
                        <?php if ($info['pid'] != 0 || empty($info)){ ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">请选择一级菜单</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" name="ParentId" id="ParentId" >
                                    <option value='0'>请选择一级菜单</option>
                                    <?php foreach ($ParentMenuList as $key => $val){ ?>
                                    	<?php if ($val['id'] == $info['pid']){ ?>
                                    		<option value="<?=$val['id']?>" selected><?=$val['menu_name']?></option>
                                    	<?php }else{ ?>
                                    		<option value="<?=$val['id']?>"><?=$val['menu_name']?></option>
                                    	<?php } ?>
                                    <?php } ?>
                                    </select>
                                    如果是添加一级菜单，请不要选择这里
                                </div>
                            </div>
                           <?php } ?>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">菜单名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="lastName" id="lastName" value="<?=$info['menu_name']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">访问地址</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="url" id="url" value="<?=$info['url']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">图标</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="icon" id="icon" value="<?=$info['icon']?>">
                                </div>
                            </div>
                             <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">菜单顺序</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="sortd" id="sortd" value="<?=$info['sortd']?>">
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
	var dt = {"ParentId":$('#ParentId option:selected',self.document).val(),"lastName":$("#lastName").val(),'sortd':$("#sortd").val(),'url':$("#url").val(),'icon':$('#icon').val()};
	
	$.post("<?=site_url("auth/doMenuSave/{$info['id']}")?>",dt,function(data){
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