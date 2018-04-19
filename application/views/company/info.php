<?php $this->load->view("main/header");?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
                        	<div class="form-group">
	                                <label class="col-sm-2 control-label">支付状态：</label>
	                                <div class="col-sm-4">
	                                    <?php
                                            	 if ($info['status'] == 0){ 
                                            		echo '<font color=red>已导未发</font>';
                                            	 }elseif ($info['status'] == 1){
                                            	 	echo '<font color=red>已发未领</font>';
                                            	 }elseif ($info['status'] == 2){
                                            	 	echo '<font color=green>已领</font>';
                                            	 }
                                            	?>
	                                </div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">联通订单号：</label>
	                                <div class="col-sm-4">
	                                    <?=$info['unorderid']?>
	                                </div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
	                            <div class="form-group">
	                                <label class="col-sm-2 control-label">手机型号：</label>
	                                <div class="col-sm-4">
	                                    <?=$mach["mach_type"]?>
	                                </div>
	                            </div>
	                            <div class="hr-line-dashed"></div>
                        	<div class="form-group">
                                <label class="col-sm-2 control-label">发展人编码：</label>
                                <div class="col-sm-4">
                                <?=$info['unicode']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">激励金额：</label>
                                <div class="col-sm-4">
                                	<?=$info['price']?>	
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">串号：</label>
                                <div class="col-sm-4">
                                	<?=$info['serialCode']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">省份：</label>
                                <div class="col-sm-4">
                                	<?=$pro['ProvinceName']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">城市：</label>
                                <div class="col-sm-4">
                                	<?=$info['cities']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地区：</label>
                                <div class="col-sm-4">
                                	<?=$info['county']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">门店信息：</label>
                                <div class="col-sm-4">
                                	<?=$info['hall']?>
                                </div>
                            </div>
                           
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">竣工时间：</label>
                                <div class="col-sm-2">
                                	<?=date("Y-m-d H:i:s",$info['imptime'])?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">导入时间：</label>
                                <div class="col-sm-2">
                                	<?=date("Y-m-d",$info['datetime'])?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">姓名：</label>
                                <div class="col-sm-2">
                                <?=$userInfo['uname']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">联系电话：</label>
                                <div class="col-sm-2">
                                	<?=$userInfo['phone']?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view("main/footer");?>
