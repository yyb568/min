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
                                        <input type="text" class="form-control" name="keywords" id="keywords" placeholder="可查询：机型"> <span class="input-group-btn"> <button type="button" class="btn btn-primary" onClick="doSearch('keyword')">搜索</button> </span>
                                    </div>
	                            </div>
                         <div class="hr-line-dashed"></div>
                            <div class="form-group">
	                                <div class="input-group col-sm-2">
	                                 <label class="col-sm-2 control-label">地区查询：</label>
                                        <button data-toggle="button" class="btn btn-primary btn-outline" type="button" onClick="switchVoid();">选择地区</button>
	                                    <input type="hidden" name="area" id="area" value="<?=$info['area'] ?>">
	                                    &nbsp;<button type="button" class="btn btn-primary" onClick="doSearch('area')">搜索</button> </span>
	                                    <div id="area_txt"></div>
                                    </div>
	                            </div>
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">开始时间：</label>
	                                <div class="col-sm-4">
	                                	<input type="text" name="starttime" id="starttime"  class="form-control layer-date" >
	                                </div>
	                            </div>
                        	<div class="form-group">
                                <label class="col-sm-2 control-label">结束时间：</label>
                                <div class="col-sm-4">
                                   <input type="text" name="endtime" id="endtime"  class="form-control layer-date" >
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="button" onClick="doSearch('timed');">查询</button>
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
	if (type == 'keyword'){
		var keywords = $("#keywords").val();
		if (!keywords){showTips("请输入查询关键词！",'error');return false;}
		window.parent.doSearch('keyword',{"keyword":keywords});
	}else if(type == 'area'){
		var area = $("#area").val();
		if (!area){showTips('请选择地区后再进行查询！','error');return false;}
		window.parent.doSearch('area',{"area":area});
	}else if(type == 'timed'){
		var starttime = $("#starttime").val();
		var endtime = $("#endtime").val();
		if (!starttime || !endtime){showTips('请选择查询时间段！','error');return false;}
		window.parent.doSearch('timed',{"start":starttime,"end":endtime});
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