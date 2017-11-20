<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form class="form-horizontal" id="form1" name="form1">
                             <div class="form-group">
                                <label class="col-sm-2 control-label">视频名称：</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="vname" id="vname" value="<?=$info['vname']?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">视频类型：</label>
                                <div class="col-sm-4">
                                    <select class="form-control m-b" name="cate_id" id="cate_id">
                                    <?php foreach ((array) $mvtypelist as $key => $val){ ?>
                                    <option value="<?=$val['id']?>" <?php if ($info['filetype'] == 0){echo 'selected';} ?>><?=$val['vname']?></option>
                                   <?php }?>
                                </select>
                                </div>
                            </div>
                           
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">上传文件：</label>
                                <div class="col-sm-10">
                                <input type="hidden" value="" id="filename" name="filename" />
                                <div id="uploader" class="wu-example">
                                    <!--用来存放文件信息-->
                                    <div id="thelist" class="uploader-list"></div>
                                    <div class="btns">
                                        <div id="picker">选择文件</div>
                                        <button id="ctlBtn" class="btn btn-default" type="button">开始上传</button>
                                    </div>
                                </div>
                                                                
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否上架：</label>
                                <div class="col-sm-2">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="1" id="status" name="status" <?php if ($info['status'] == 1){echo 'checked';}?>>上架</label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="0" id="status" name="status" <?php if ($info['status'] == 0){echo 'checked';}?>>不上架</label>
                                    </div>
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
<script>
var BASE_URL = '<?=static_url('js')?>';
var UP_URL = '<?=site_url("bdupload/uploads")?>';
</script>
<link rel="stylesheet" type="text/css" href="<?=static_url("js")?>webuploader-0.1.5/webuploader.css">
<script type="text/javascript" src="<?=static_url("js")?>webuploader-0.1.5/webuploader.js"></script>
<script type="text/javascript" src="<?=static_url("js")?>webuploader-0.1.5/getting-started.js"></script>
<script>
function doSubmit(isgo){
    if (!isgo){isgo=0;}     // 第一次上传的时候需要验证上次上传时间
    var task_source = $("#cate_id option:selected").val();
    var taskname = $("#vname").val();
    var filename = $("#filename").val();
    var status = $("input[name='status']:checked").val()
    if (!task_source || !taskname || !filename){showTips('请填写任务信息！','error');return false;}
    $.post("<?=site_url("mvmanage/doSave/{$info['id']}")?>?isgo="+isgo,{tasksource:task_source,taskname:taskname,filename:filename,status:status},function(data){
        if (data.status == 0){
            showTips("保存成功！",'success');
        }else{
            layer.confirm(data.info, {
                  shadeClose:true,
                  btn: ['确定上传','放弃'] //按钮
                }, function(){
                    layer.msg('2秒后自动重新提交！', {icon: 1});
                    setTimeout(function(){doSubmit(1);},2000);
                });
            
        }
    },'json');
}
</script>
