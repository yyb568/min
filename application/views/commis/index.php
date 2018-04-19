<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    
    <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
               
                	<button class="btn btn-primary " type="button" onClick="addNew();"><i class="fa fa-check"></i>&nbsp;新增数据</button>
                	<button class="btn btn-danger " type="button" onClick="uploads();"><i class="fa fa-upload"></i>&nbsp;&nbsp;<span class="bold">导入数据</span></button>
                	
                	
					<button class="btn btn-danger " type="button" onClick="SearchTime();"><i class="fa fa-paste"></i>&nbsp;数据查询</button>
					
					<button class="btn btn-danger " type="button" onClick="Exload();"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>&nbsp;导出数据</button>
					<button class="btn btn-success " type="button" onClick="window.location.reload();"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>&nbsp;刷新页面</button>
                </div>
            </div>
        </div>
    
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>佣金管理 </h5>
                        </div>
                        <div class="ibox-content">
                            <table class="footable table table-stripped" data-page-size="8" data-filter=#filter>
                                <thead>
                                        <tr>
                                            <th>姓名</th>
                                            <th>发展人编码</th>
                                            <th>激励金额</th>
                                            <th>联通订单号</th>
                                            <th>手机型号</th>
                                            <th>串号</th>
                                            <th>地区</th>
                                            <th>竣工时间</th>
                                            <th>支付</th>
                                            <th>操作者</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                   
                                        <tr class="gradeX">
                                            <td><a href="javascript:void(0);LockInfo(<?=$val['id']?>);" class="font-bold"></a></td>
                                            <td><a href=""></a></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
	                                            <div class="btn-group">
					                                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" aria-expanded="false">操作 <span class="caret"></span></button>
					                                <ul class="dropdown-menu">
					                                	<li><a href="" class="font-bold">查看详情</a></li>
					                                	
					                                	<li class="divider"></li>
					                                    <li><a href="" class="font-bold">编辑</a></li>
					                                   
					                                   
					                                </ul>
					                            </div>
                                            </td>
                                        </tr>
                                   
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php $this->load->view("main/footer"); ?>
<script>
//查看兑付详细资料
function LockInfo(user_id){
	showFarme('查看兑付资料','<?=site_url("commis/lockInfo")?>/'+user_id,'80%','90%');
}

//添加新分组
function addNew(){
	showFarme('新增一条','<?=site_url("commis/doAdd")?>','80%','90%');
}
//修改分组
function Edit(user_id){
	showFarme('编辑资料','<?=site_url("commis/doAdd")?>/'+user_id,'80%','90%');
}
//删除分组
function Del(group_id){
		layer.confirm('您确定要删除吗？<br>删除后该笔兑付将无法到达用户手中！',{
		    btn: ['确定','取消'] //按钮
		}, function(){
			$.get("<?=site_url("commis/DelInfo") ?>/"+group_id,function(data){
				if (data.status == 1){
					showTips(data.info,'error');
				}else{
					showTips('删除成功！','success');
					setTimeout(function(){window.location.reload();},1000);
				}
			},'json');
		});
}
// 查询当天导入的数据
function toDaySearch(){
	window.location.href = '<?=site_url("commis/index?today=1")?>';
}
//按照时间方式进行查询
function SearchTime(){
	showFarme('佣金查询','<?=site_url("commis/searchType")?>','50%','80%');
}
//执行时间毁掉查询
function doSearch(type,Obj){
	layer.closeAll('iframe');
	if (type == 'keyword'){
		window.location.href = '<?=site_url("commis/index?keyword=")?>'+Obj.keyword+'&searchtype=1';
	}else if(type == 'area'){
		window.location.href = '<?=site_url("commis/index?area=")?>'+Obj.area+'&searchtype=1';
	}else if(type == 'timed'){
		window.location.href = '<?=site_url("commis/index")?>?starttime='+Obj.start+'&endtime='+Obj.end+'&searchtype=1';
	}else if(type == 'day'){
		window.location.href = '<?=site_url("commis/index")?>?startdate='+Obj.start+'&enddate='+Obj.end+'&searchtype=1';
	}else if(type == 'match'){
		var match = Obj.match_id.join(",");
		window.location.href = '<?=site_url("commis/index")?>?match='+match;
	}
}

// 进行结算
function startBill(){
	showFarme('结算操作','<?=site_url("commis/startBill")?>/','50%','60%');
}

// 导出文件
function Exload(){
	var baseUrl = window.location.href;			//获取当前页面的url
	if (baseUrl.indexOf("?") > 0){
		baseUrl += '&expload=1';						// 增加一个参数标示导出
	}else{
		baseUrl += '?expload=1';						// 增加一个参数标示导出
	}
	window.location.href = baseUrl;
}

//上传规则文件
function uploads(type){
	showFarme('导入兑付数据','<?=site_url("commis/importData")?>?type='+type,'70%','90%');
}

// 申请财务打款
function Funds(_totalprice){
	showFarme('财务打款申请','<?=site_url("financial/funds")?>/?_totalprice='+_totalprice,'70%','90%');
}
</script>