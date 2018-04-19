<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">发展人编码：</label>
	                                <div class="col-sm-2">
	                                    <input type="text" class="form-control" name="unicode" id="unicode" value="<?=$info['unicode']?>">
	                                </div>
	                            </div>
                        	<div class="form-group">
                                <label class="col-sm-2 control-label">订单号：</label>
                                <div class="col-sm-2">
                                	<input type="text" class="form-control" name="order_id" id="order_id" value="<?=$info['unorderid']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">手机型号：</label>
                                <div class="col-sm-2">
                                	<select class="form-control m-b" name="phonetype" id="phonetype">
                                        <option value="">请选择</option>
                                    	<?php foreach($phonetype as $key => $val){ ?>
                                        <option value="<?=$val['id']?>" <?php if ($val['id'] == $info['phonetypeid']){echo 'selected';}?>><?=$val['mach_type']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">串号：</label>
                                <div class="col-sm-2">
                                	<input type="text" class="form-control" name="serialCode" id="serialCode" value="<?=$info['serialCode']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">所属省：</label>
                                <div class="col-sm-2">
                                	<select class="form-control m-b" name="area" id="area">
                                        <option value="">请选择</option>
                                    	<?php foreach($pro as $key => $val){ ?>
                                        <option value="<?=$val['ProvinceID']?>" <?php if ($val['ProvinceID'] == $info['area']){echo 'selected';}?>><?=$val['ProvinceName']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">竣工时间：</label>
                                <div class="col-sm-2">
                                	<input type="text" class="form-control" name="imptime" id="imptime" value="<?=!empty($info['imptime']) ? date("Y-m-d H:i:s",$info['imptime']) : ''?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">订单类型：</label>
                                <div class="col-sm-2">
                                <input type="text" class="form-control" name="ordertype" id="ordertype" value="<?=$info['ordertype']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="button" onClick="doSubmit();">保存信息</button>
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

//保存配置信息
function doSubmit(){
	var dt = {
			"unicode" : $("#unicode").val(),
			"order_id" : $("#order_id").val(),
			"phonetype" : $("#phonetype option:selected").val(),
			"serialCode" : $("#serialCode").val(),
			"area" : $("#area option:selected").val(),
			"imptime" : $("#imptime").val(),
			"ordertype" : $("#ordertype").val(),
		}
	$.post("<?=site_url("commis/doSave/{$info['id']}")?>",dt,function(data){
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

var start={elem:"#imptime",format:"YYYY-MM-DD hh:mm:ss",max:"2099-06-16 23:00:00",istime:true,istoday:false,choose:function(a)	{end.min=a;end.start=a}};
laydate(start);
</script>