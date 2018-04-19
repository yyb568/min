<?php $this->load->view("main/header"); ?>
<link href="<?=static_url("css") ?>bootstrap-multiselect.css" rel="stylesheet">
<script src="<?=static_url("js") ?>bootstrap-multiselect.js"></script>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
	                            <div class="form-group">
	                                <div class="input-group col-sm-2">
                                        <input type="text" class="form-control" name="keywords" id="keywords" placeholder="可查询：发展人编码、订单号、金额、串号"> <span class="input-group-btn"> <button type="button" class="btn btn-primary" onClick="doSearch('keyword')">搜索</button> </span>
                                    </div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">导入时间查询：</label>
	                                <div class="col-sm-10">
	                                    <div class="row">
	                                        <div class="col-md-2">
	                                            <input type="text" name="start" id="start"  class="form-control layer-date" >
	                                        </div>
	                                        <div class="col-md-2">
	                                            <input type="text" name="end" id="end"  class="form-control layer-date" >
	                                        </div>
	                                    </div>
	                                    &nbsp;<button type="button" class="btn btn-primary" onClick="doSearch('day')">搜索</button> </span>
	                                </div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
	                            <div class="form-group">
	                                <div class="input-group col-sm-2">
	                                 <label class="col-sm-2 control-label">机型查询：</label>
	                                  <div class="col-sm-6">
                                       <select class="form-control m-b" name="macth_id[]" id="macth_id" multiple="multiple">
	                                    	<?php foreach($machlist as $key => $val){?>
	                                        <option value="<?=$val['id']?>"><?=$val['mach_type']?></option>
	                                        <?php } ?>
	                                    </select>
	                                     &nbsp;<button type="button" class="btn btn-primary" onClick="doSearch('match')">搜索</button>
	                                    </div>
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
	                            <div class="hr-line-dashed"></div>
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">竣工时间查询：</label>
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
var multiSelectOption={
        maxHeight:300,
        numberDisplayed:3,   
        optionClass: function(element) {  
            var value = $(element).parent().find($(element)).index();  
            if (value%2 == 0) {  
                return 'even';  
            }  
            else {  
                return 'odd';  
            }  
        },
        includeSelectAllOption: true,  
        selectAllText:'全选/取消全选',
        enableFiltering: true,  
        selectAllJustVisible: true ,
        buttonWidth: '300px',  
        dropRight: true 
};
$(document).ready(function() {
    $('#macth_id').multiselect(multiSelectOption);
});
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
	}else if(type == 'day'){
		var starttime = $("#start").val();
		var endtime = $("#end").val();
		if (!starttime || !endtime){showTips('请选择查询时间段！','error');return false;}
		window.parent.doSearch('day',{"start":starttime,"end":endtime});
	}else if (type == 'match'){

		var match_id = $("#macth_id",self.document).val();
		if (!match_id){showTips("请选择机型","error"); return false;}
		
		window.parent.doSearch('match',{"match_id":match_id});
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

	var start2={elem:"#start",format:"YYYY-MM-DD",max:"2099-06-16",istime:false,istoday:false,choose:function(a)	{end.min=a;end.start=a}};
	var end2={elem:"#end",format:"YYYY-MM-DD",max:"2099-06-16",istime:false,istoday:false,choose:function(a){start.max=a}};
	laydate(start2);laydate(end2);
</script>