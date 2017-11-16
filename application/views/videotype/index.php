<?php $this->load->view("main/header"); ?>

<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    
    <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                	<button class="btn btn-danger " type="button" onClick="addNew();"><i class="fa fa-check"></i>&nbsp;新增类型</button>
					<!-- <button class="btn btn-primary " type="button" onClick="SearchTime();"><i class="fa fa-paste"></i>&nbsp;查询条件</button> -->
					<!-- <button class="btn btn-danger " type="button" onClick="Exload();"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>&nbsp;导出数据</button> -->
					<button class="btn btn-success " type="button" onClick="window.location.reload();"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>&nbsp;刷新页面</button>
					
                </div>
            </div>
        </div>
    
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>视频类型管理</h5>
                        </div>
                        <div class="ibox-content">
                            <table class="footable table table-stripped" data-page-size="8" data-filter=#filter>
                                <thead>
                                        <tr>
                                            <th>类型名称</th>
                                            <th>是否显示</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach((array)$list as $key => $val){ ?>
                                        <tr class="gradeX">
                                            <td><?=$val['vname']?></td>
                                            <td><?php if ($val['status'] == 1){ ?>
                                                 显示
                                                <?php }else{?>
                                                 不显示
                                                <?php }?>
                                            </td>
                                            <td>
	                                            <div class="btn-group">
					                                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" aria-expanded="false">操作 <span class="caret"></span></button>
					                                <ul class="dropdown-menu">
					                                    <li><a href="javascript:void(0);Edit(<?=$val['id']?>);" class="font-bold">编辑</a></li>
					                                    <li class="divider"></li>
					                                    <li><a href="javascript:void(0);Del(<?=$val['id']?>);">删除</a></li>
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
//查看会员详细资料
function LockInfo(user_id){
	showFarme('查看详情','<?=site_url("videotype/lockInfo")?>/'+user_id,'80%','90%');
}
//添加新分组
function addNew(){
	showFarme('新增类型','<?=site_url("videotype/doAdd")?>','80%','90%');
}
//修改分组
function Edit(user_id){
	showFarme('编辑类型','<?=site_url("videotype/doAdd")?>/'+user_id,'80%','90%');
}
//删除分组
function Del(group_id){
		layer.confirm('您确定要删除吗？', {
		    btn: ['确定','取消'] //按钮
		}, function(){
			$.get("<?=site_url("videotype/DelInfo") ?>/"+group_id,function(data){
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