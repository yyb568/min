<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
	                            <div class="form-group">
	                                <div class="input-group col-sm-2">
                                        <input type="text" class="form-control" name="keywords" id="keywords" placeholder="可查询：手机号、姓名"> <span class="input-group-btn"> <button type="button" class="btn btn-primary" onClick="doSearch('keyword')">搜索</button> </span>
                                    </div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">注册时间查询：</label>
	                                <div class="col-sm-10">
	                                    <div class="row">
	                                        <div class="col-md-2">
	                                            <input type="text" name="starttime" id="starttime"  class="form-control layer-date" >
	                                        </div>
	                                        <div class="col-md-2">
	                                            <input type="text" name="endtime" id="endtime"  class="form-control layer-date" >
	                                        </div>
	                                    </div>
	                                    &nbsp;<button type="button" class="btn btn-primary" onClick="doSearch('timed')">搜索</button> </span>
	                                </div>
	                                	
	                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view("main/footer");?>
<script src="<?=static_url("js")?>/plugins/layer/laydate/laydate.js"></script>
<script>
function doSearch(type){
	var keywords = $("#keywords").val();
	var area = $("#area").val();
	var starttime = $("#starttime").val();
	var endtime = $("#endtime").val();
	if (type == 'keyword'){
		if (!keywords){showTips("请输入查询关键词！",'error');return false;}
		window.parent.doSearch('keyword',{"keyword":keywords,"area":area,"start":starttime,"end":endtime,"area":area});
	}else if(type == 'area'){
		if (!area){showTips('请选择地区后再进行查询！','error');return false;}
		window.parent.doSearch('area',{"area":area,"keyword":keywords,"start":starttime,"end":endtime});
	}else if(type == 'timed'){
		if (!starttime || !endtime){showTips('请选择查询时间段！','error');return false;}
		window.parent.doSearch('timed',{"start":starttime,"end":endtime,"keyword":keywords,"area":area});
	}
}

//选择地区
function switchVoid(){	   
	var area = $("#area").val();
	showFarme('选择地区','<?=site_url("area/switch_list/?userType=1&area=")?>'+area,'70%','90%');
}
/**
 * 地区选择框回调获取地址
 */
function parentSwitchArea(pro,cty,dis){
	province = pro;
	city = cty;
	district = dis;
	$("#area").val(province+","+city+","+district);
	$("#area_txt").html("已选择").css("color","green");
}

	var start={elem:"#starttime",format:"YYYY-MM-DD hh:mm:ss",max:"2099-06-16 23:00:00",istime:true,istoday:false,choose:function(a)	{end.min=a;end.start=a}};
	var end={elem:"#endtime",format:"YYYY-MM-DD hh:mm:ss",max:"2099-06-16 23:59:59",istime:true,istoday:false,choose:function(a){start.max=a}};
	laydate(start);laydate(end);
</script>