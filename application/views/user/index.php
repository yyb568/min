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
    
    <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                	<button class="btn btn-primary " type="button" onClick="addNew();"><i class="fa fa-check"></i>&nbsp;新增会员</button>
					<button class="btn btn-primary " type="button" onClick="SearchTime();"><i class="fa fa-paste"></i>&nbsp;查询条件</button>
					<button class="btn btn-danger " type="button" onClick="Exload();"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>&nbsp;导出数据</button>
					<button class="btn btn-success " type="button" onClick="window.location.reload();"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>&nbsp;刷新页面</button>
					
                </div>
            </div>
        </div>
    
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>会员管理 </h5>
                        </div>
                        <div class="ibox-content">
                            <table class="footable table table-stripped" data-page-size="8" data-filter=#filter>
                                <thead>
                                        <tr>
                                            <th>姓名</th>
                                            <th>手机号(账号)</th>
                                            <th>余额</th>
                                            <th>所属地区</th>
                                            <th>资料</th>
                                            <th>状态</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach((array)$list as $key => $val){ ?>
                                        <tr class="gradeX">
                                            <td><?=$val['uname']?></td>
                                            <td><a href="<?=site_url("user/index?phone={$val['phone']}")?>"><?=$val['phone']?></a></td>
                                            <td><a href="javascript:void(0);" title="累计收入"><?=$val['grandtotal']?></a> / <a href="javascript:void(0);" title="账户总余额"><?=$val['totalprice']?></a> / <a href="javascript:void(0);" title="昨日收入"><?=$val['price']?></a></td>
                                            <td><a href="<?=site_url("user/index?qq={$val['qq']}")?>"><?=$val['qq']?></a></td>
                                            <td><a href="<?=site_url("user/index?pro={$val['province']}")?>"><?=$_provinceList[$val['province']]['ProvinceName'] ?></a> - <a href="<?=site_url("user/index?city={$val['city']}")?>"><?=$_cityList[$val['city']]['CityName'] ?></a> - <a href="<?=site_url("user/index?dis={$val['district']}")?>"><?=$_districtList[$val['district']]['DistrictName'] ?></a></td>
                                            <td>
                                            <?php if (empty($val['uname']) || empty($val['uid']) || empty($val['phone']) || empty($val['unicode']) || empty($val['province'])){ ?>
                                            	<a href="<?=site_url("user/index?sd=1{$_params}")?>"><font color=red>未完善</font></a>
                                            <?php }else{ ?>
                                            	<a href="<?=site_url("user/index?sd=2{$_params}")?>"><font color=green>已完善</font></a>
                                            <?php } ?>
                                            </td>
                                            <td>
                                            <?php if ($val['status'] == 0){ ?>
                                            	<a href="<?=site_url("user/index?st=-1&phone={$phone}&unicode={$unicode}&pro={$pro}&city={$city}&dis={$dis}&keyword={$keyword}&starttime={$start}&endtime={$end}&area={$area}")?>"><font color=red>冻结</font>
                                            <?php }elseif ($val['status'] == 1){ ?>
                                            	<a href="<?=site_url("user/index?st={$val['status']}&phone={$phone}&unicode={$unicode}&pro={$pro}&city={$city}&dis={$dis}&keyword={$keyword}&starttime={$start}&endtime={$end}&area={$area}")?>"><font color=green>正常</font>
                                            <?php } ?>
                                            </a></td>
                                            <td>
	                                            <div class="btn-group">
					                                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" aria-expanded="false">操作 <span class="caret"></span></button>
					                                <ul class="dropdown-menu">
					                                	<li><a href="javascript:void(0);LockInfo(<?=$val['id']?>);" class="font-bold">查看详情</a></li>
					                                	<li class="divider"></li>
					                                	<li><a href="javascript:void(0);Rest(<?=$val['id']?>);" class="font-bold">重置登录</a></li>
					                                	<li class="divider"></li>
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
	showFarme('查看会员详细资料','<?=site_url("user/lockInfo")?>/'+user_id,'80%','90%');
}

//重置用户登录
function Rest(group_id){
	layer.confirm('您确定要重置用户登录吗？', {
	    btn: ['确定','取消'] //按钮
	}, function(){
		$.get("<?=site_url("user/Rest")?>/"+group_id,function(data){
			if (data.status == 1){
				showTips(data.info,'error');
			}else{
				showTips('重置成功！','success');
				setTimeout(function(){window.location.reload();},1000);
			}
		},'json');
	});
}
//添加新分组
function addNew(){
	showFarme('新增会员','<?=site_url("user/doAdd")?>','80%','90%');
}
//修改分组
function Edit(user_id){
	showFarme('编辑会员资料','<?=site_url("user/doAdd")?>/'+user_id,'80%','90%');
}
//删除分组
function Del(group_id){
		layer.confirm('您确定要删除吗？', {
		    btn: ['确定','取消'] //按钮
		}, function(){
			$.get("<?=site_url("user/DelInfo") ?>/"+group_id,function(data){
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
	showFarme('会员信息查询','<?=site_url("user/searchType")?>/','50%','75%');
}
//执行时间毁掉查询
function doSearch(type,Obj){
	layer.closeAll('iframe');
	if (type == 'keyword'){
		window.location.href = '<?=site_url("user/index?phone={$phone}&unicode={$unicode}&pro={$pro}&city={$city}&dis={$dis}&st={$st}&starttime={$start}&endtime={$end}&area={$area}&keyword=")?>'+Obj.keyword+'&area='+Obj.area+'&starttime='+Obj.start+'&endtime='+Obj.end+'&searchtype=1';
	}else if(type == 'area'){
		window.location.href = '<?=site_url("user/index?phone={$phone}&unicode={$unicode}&pro={$pro}&city={$city}&dis={$dis}&st={$st}&keyword={$keyword}&starttime={$start}&endtime={$end}&area=")?>'+Obj.area+'&keyword='+Obj.keyword+'&starttime='+Obj.start+'&endtime='+Obj.end+'&searchtype=1';
	}else if(type == 'timed'){
		window.location.href = '<?=site_url("user/index?phone={$phone}&unicode={$unicode}&pro={$pro}&city={$city}&dis={$dis}&st={$st}&keyword={$keyword}&area={$area}")?>&keyword='+Obj.keyword+'&starttime='+Obj.start+'&endtime='+Obj.end+'&area='+Obj.area+'&searchtype=1';
	}
}

// 导出文件
function Exload(){
	var baseUrl = window.location.href;			//获取当前页面的url

	baseUrl += '&expload=1';						// 增加一个参数标示导出
	window.location.href = baseUrl;
}
</script>