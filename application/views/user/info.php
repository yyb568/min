<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">登录账号：</label>
                                <div class="col-sm-4">
                                    <?=$info['uname']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">账户余额：</label>
                                <div class="col-sm-4">
                                    <?=$val['totalprice']?>
                                </div>
                            </div>
	                        <div class="hr-line-dashed"></div>
                        	<div class="form-group">
                                <label class="col-sm-2 control-label">性别：</label>
                                <div class="col-sm-4">
                                <?php 
                                if ($info['sex'] == 1){
                                	echo '男';
                                }elseif ($info['sex'] == 2){
                                	echo '女';
                                }else{
                                	echo '未填写';
                                } ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">手机号：</label>
                                <div class="col-sm-4">
                                	<?=$info['phone']?>	
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">等级：</label>
                                <div class="col-sm-4">
                                <?php if ($val['level'] == 0){?>
                                    普通用户
                                <?php }else{ ?>
                                    VIP会员
                                <?php }?>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">昵称：</label>
                                <div class="col-sm-4">
                                	<?=$info['nick']?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态：</label>
                                <div class="col-sm-2">
                                	<?php if ($info['status'] == 0){echo '<font color=red>冻结</font>';}else{echo '<font color=green>正常</font>';} ?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">注册时间：</label>
                                <div class="col-sm-2">
                                <?=date("Y-m-d H:i:s",$info['created'])?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">最后登录时间：</label>
                                <div class="col-sm-2">
                                	<?=date("Y-m-d H:i:s",$info['last_time'])?>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">资料最后更新时间：</label>
                                <div class="col-sm-2">
                                	<?=date("Y-m-d H:i:s",$info['last_update'])?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->load->view("main/footer");?>
