<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1" target="_blank" action="<?=site_url("commis/startcompute")?>">
	                            <div class="form-group">
	                            <label class="col-sm-2 control-label">结算方式</label>
                                    <div class="col-sm-10">
	                                    <label class="checkbox-inline"><input type="radio" value="1" id="startbill" name="startbill" onClick="compute(1);">&nbsp;结算全部未结算</label>
	                                    <label class="checkbox-inline"><input type="radio" value="2" id="startbill" name="startbill" onClick="compute(2);">&nbsp;结算当日导入</label>
                                	</div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
                                    <div class="col-sm-10">
	                                    <button class="btn btn-primary" id="btn_startd" type="button" disabled onClick="startd();">开始结算</button>
	                                    <button class="btn btn-w-m btn-danger" id="btn_funds" style="display:none;" type="button" disabled onClick="startFunds();">申请资金</button>
	                                    <div id="msg"></div>
                                	</div>
	                            </div>
	                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php $this->load->view("main/footer");?>
<script>
var systotalprice = <?=isset($fundinfo['total']) ? $fundinfo['total'] : 0?>;
// 开始结算
function startd(){
	layer.confirm('您确定要结算吗？<br>请再次确认！', {
	    btn: ['确定','取消'] //按钮
	}, function(index){
		$("#form1").submit();
		layer.close(index);
	});
}
// 结算方式计算需要多少钱等相关信息
function compute(value){
	$("#btn_startd").attr("disabled",true);
	$("#msg").html("请稍后，正在计算结算金额 ... ").css("color","red");
	$.get("<?=site_url("commis/compute")?>/"+value,function(data){
		if (data.status == 1){
			$("#msg").html(data.info).css("color","red");
		}else{
			if (data.info.price > systotalprice){
				$("#msg").html("当前账户余额不足，请您申请财务打款！<br>账户余额：<b>"+systotalprice+"元</b>； 结算总金额：<b>"+data.info.price+"元</b>；结算总条数：<b>"+data.info.total+"条订单</b>").css("color","red");
				$("#btn_funds").show().attr("disabled",false).attr("price",data.info.price);
			}else{
				$("#msg").html("结算总金额：<b>"+data.info.price+"元</b>；结算总条数：<b>"+data.info.total+"条订单</b><br> 当前账户余额：<b>"+systotalprice+"</b> 元").css("color","red");
				$("#btn_startd").attr("disabled",false);
			}
		}
	},'json');
}

// 申请财务打款
function startFunds(){
	var _totalprice = $("#btn_funds").attr("price");
	window.parent.Funds(_totalprice);
}
</script>