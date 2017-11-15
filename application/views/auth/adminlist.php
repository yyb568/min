<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>管理员列表 </h5>
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
                                            <th>账号</th>
                                            <th>姓名</th>
                                            <th>最后登录时间</th>
                                            <th>最后登录ip</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach((array)$list as $key => $val){ ?>
                                        <tr class="gradeX">
                                            <td><?=$val['username']?></td>
                                            <td><?=$val['uname']?></td>
                                            <td><?=$val['last_time'] ? date("Y-m-d H:i:s",$val['last_time']) : '<font color=red>从未登陆</font>' ?></td>
                                            <td><?=$val['last_ip'] ? $val['last_ip'] : '<font color=red>从未登陆</font>';?></td>
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
	showFarme('添加管理员','<?=site_url("auth/adminAdd")?>','70%','90%');
}
//修改分组
function group_edit(group_id){
	showFarme('编辑管理员','<?=site_url("auth/adminAdd")?>/'+group_id,'80%','90%');
}
//删除分组
function group_del(group_id){
		layer.confirm('您确定要删除吗？<br>删除后可能会影响该组下的用户登录操作！', {
		    btn: ['确定','取消'] //按钮
		}, function(){
			$.get("<?=site_url("auth/delAdmin") ?>/"+group_id,function(data){
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