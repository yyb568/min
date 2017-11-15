<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>系统菜单列表 </h5>
                            <div class="ibox-tools">
                                <a class="close-link" title="点击新增" href="javascript:void(0);addNew();">
                                    <i class="fa fa-wrench"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <table class="footable table table-stripped" data-page-size="8" data-filter=#filter>
                                <thead>
                                        <tr>
                                            <th>菜单名称</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach((array)$list as $key => $val){ ?>
                                        <tr class="gradeX">
                                            <td>
                                            <?php 
												if ($val['pid'] == 0){
													echo $val['menu_name'];
												}else{
													echo $list[$val['pid']]['menu_name'].' --> '.$val['menu_name'];
												}
                                            ?>
                                            </td>
                                            <td>
	                                            <div class="btn-group">
					                                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" aria-expanded="false">操作 <span class="caret"></span>
					                                </button>
					                                <ul class="dropdown-menu">
					                                    <li><a href="javascript:void(0);group_edit(<?=$val['id']?>);" class="font-bold">修改</a></li>
					                                    <li class="divider"></li>
					                                    <li><a href="javascript:void(0);group_del(<?=$val['id']?>);">删除</a></li>
					                                </ul>
					                            </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                            </table>
                            <?=$Page; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php $this->load->view("main/footer"); ?>
<script>
//添加新分组
function addNew(){
	showFarme('添加系统菜单','<?=site_url("auth/menuAdd")?>','80%','90%');
}
//修改分组
function group_edit(group_id){
	showFarme('编辑系统菜单','<?=site_url("auth/menuAdd")?>/'+group_id,'50%','70%');
}
//删除分组
function group_del(group_id){
		layer.confirm('您确定要删除吗？<br>删除后可能会影响该组下的用户登录操作！', {
		    btn: ['确定','取消'] //按钮
		}, function(){
			$.get("<?=site_url("auth/delMenu") ?>/"+group_id,function(data){
				if (data.status == 1){
					showTips(data.info,'error');
				}else{
					showTips('删除成功！','success');
					setTimeout(function(){window.location.reload();},1000);
				}
			},'json');
		});
}
</script>