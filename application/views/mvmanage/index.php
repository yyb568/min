<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">

<div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
            	<button class="btn btn-primary " type="button" onClick="addNew();"><i class="fa fa-check"></i>&nbsp;新增</button>
				<button class="btn btn-primary " type="button" onClick="SearchTime();"><i class="fa fa-paste"></i>&nbsp;查询条件</button>
				<button class="btn btn-success " type="button" onClick="window.location.reload();"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>&nbsp;刷新页面</button>
				
            </div>
        </div>
    </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>视频管理</h5>
                    </div>
                    <div class="ibox-content">
                        <table class="footable table table-stripped" data-page-size="8" data-filter=#filter>
                            <thead>
                                    <tr>
                                        <th><input type="checkbox" name="all" id="all" onclick="selAll()" /></th>
                                        <th>视频名称</th>
                                        <th>类型</th>
                                        <th>是否收费</th>
                                        <th>添加时间</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach((array)$list as $key => $val){ ?>
                                    <tr class="gradeX">
                                        <td><input type="checkbox" name="all[]" id="all"  value="<?=$val['id']?>" /></td>
                                        <td><?=$val['mach_type']?></td>
                                        <td><a href="<?=site_url("excation/index?buss_id={$val['buss_id']}")?>"><?=$bussList[$val['buss_id']]['buss_name']?></a></td>
                                        <td><a href="<?=site_url("excation/index?price={$val['price']}")?>"><?=$val['price']?></a></td>
                                        <td><font color=red><?=date("Y-m-d H:i:s",$val['starttime'])?></font></td>
                                        <td>
                                            <div class="btn-group">
				                                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" aria-expanded="false">操作 <span class="caret"></span>
				                                </button>
				                                <ul class="dropdown-menu">
				                                    <li><a href="javascript:void(0);group_edit(<?=$val['id']?>);" class="font-bold">修改</a></li>
				                                    <li class="divider"></li>
				                                    <li><a href="javascript:void(0);Del(<?=$val['id']?>);">删除</a></li>
				                                </ul>
				                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                        </table>
                        <div><button class="btn btn-primary " type="button" onClick="remove();"><i class="fa fa-check"></i>&nbsp;批量删除</button></div>
                        <?=$Page; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view("main/footer"); ?>
<script>
//全选
var isCheckAll = false;
//批量全选
function selAll(){
    if (isCheckAll){
        $(":checkbox").attr("checked",false);   //设置所有复选框未勾选
        isCheckAll = false;
    }else{
        $(":checkbox").attr("checked",true); //设置所有复选框默认勾选
        isCheckAll = true;
    }
}

//批量删除
function remove(){
    checked = [];
    $('input:checkbox:checked').each(function() {
        checked.push($(this).val());
    });
    layer.confirm('您确定要删除吗？', {
        btn: ['确定','取消'] //按钮
    }, function() {
        if (checked == '') {
            showTips('请选择要删除的机型！', 'error');
            return false;
        }

        $.get("<?=site_url("mvmanage/DelAll")?>", {ids: checked}, function (data) {
            if (data.status == 1) {
                showTips(data.info, 'error');
            } else {
                showTips(data.info, 'success');
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            }
        }, 'json');
    });
}
//添加新分组
function addNew(){
	showFarme('新增视频','<?=site_url("mvmanage/doAdd")?>','80%','90%');
}
//修改分组
function group_edit(group_id){
	showFarme('编辑视频信息','<?=site_url("mvmanage/doAdd")?>/'+group_id,'80%','90%');
}
//删除分组
function Del(group_id){
		layer.confirm('您确定要删除吗？！', {
		    btn: ['确定','取消'] //按钮
		}, function(){
			$.get("<?=site_url("mvmanage/DelInfo") ?>/"+group_id,function(data){
				if (data.status == 1){
					showTips(data.info,'error');
				}else{
					showTips('删除成功！','success');
					setTimeout(function(){window.location.reload();},1000);
				}
			},'json');
		});
}
//按照时间方式进行查询
function SearchTime(){
	showFarme('按照时间方式查询','<?=site_url("excation/searchType")?>/','70%','90%');
}
//执行时间毁掉查询
function doSearch(type,Obj){
	layer.closeAll('iframe');
	if (type == 'keyword'){
		window.location.href = '<?=site_url("mvmanage/index?keyword=")?>'+Obj.keyword+'&searchtype=1';
	}else if(type == 'area'){
		window.location.href = '<?=site_url("mvmanage/index?area=")?>'+Obj.area+'&searchtype=1';
	}else if(type == 'timed'){
		window.location.href = '<?=site_url("mvmanage/index")?>?starttime='+Obj.start+'&endtime='+Obj.end+'&searchtype=1';
	}
}
</script>