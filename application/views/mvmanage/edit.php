<?php $this->load->view("main/header");?>
<link rel="stylesheet" type="text/css" href="<?=static_url("js")?>webuploader-0.1.5/style.css">
<link rel="stylesheet" type="text/css" href="<?=static_url("js")?>webuploader-0.1.5/webuploader.css">
<link rel="stylesheet" type="text/css" href="<?=static_url("css")?>advert/palette-color-picker.css">
<body class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal" id="form1" name="form1">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">视频名称：</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" name="title" id="title" value="<?=$info['title']?>">
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                <label class="col-sm-2 control-label">视频类型：</label>
                                <div class="col-sm-5">
                                    <select class="form-control m-b" name="news_type" id="news_type">
                                        <option value="">请选择</option>
                                        <?php foreach($list as $key => $val){ ?>
                                        <option value="<?=$val['id']?>" <?php if ($val['id'] == $info['news_type']){echo 'selected';}?>><?=$val['cname']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group" id="imgup">
                            <label class="col-sm-2 control-label">上传视频：</label>
                                <div class="col-sm-4">
                                    <div id="uploader" class="wu-example">
                                        <div class="queueList">
                                            <div id="dndArea" class="placeholder">
                                              <div id="filePicker">上传视频</div>
                                            </div>
                                            <?php if (!empty($info['image'])){?>
                                            <ul class="filelist" id="lie"><li id="WU_FILE_0" class="state-complete"><p class="title"><font color="green"><?=$info['image'] ?></font></p><span class="success"></span></li></ul>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                             <input type="hidden" value="<?=$info['image'] ?>" id="filenames" name="filenames">
                             </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否上架：</label>
                                <div class="col-sm-2">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="1" id="isflag" name="isflag" <?php if ($info['isflag'] == 1){echo 'checked';}?>>上架</label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" value="0" id="isflag" name="isflag" <?php if ($info['isflag'] == 0){echo 'checked';}?>>下架</label>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">视频内容：</label>
                                <div class="col-sm-4">
                                    <textarea name="myEditor" id="myEditor" style="width:700px;height:300px;"><?=$info['content'] ?></textarea>
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
<script type="text/javascript" src="<?=static_url("js")?>palette-color-picker.js"></script>
<script type="text/javascript">
// 添加全局站点信息
var BASE_URL = '<?=static_url("js")?>webuploader-0.1.5';
var UP_URL = '<?=site_url("bdupload/uploads")?>';
</script>
<script src="<?=static_url("js")?>/plugins/layer/laydate/laydate.js"></script>
<script type="text/javascript" src="<?=static_url("js")?>webuploader-0.1.5/webuploader.js"></script>
<script type="text/javascript" src="<?=static_url("js")?>webuploader-0.1.5/demo.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=static_url("js")?>ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="<?=static_url("js")?>ueditor/ueditor.all.js"> </script>
<script type="text/javascript" charset="utf-8" src="<?=static_url("js")?>ueditor/lang/zh-cn/zh-cn.js"></script>
<script>
var ue = UE.getEditor('myEditor');
//保存配置信息
function doSubmit(){
    $.post("<?=site_url("newmessage/news/doSave/{$info['id']}")?>",$("#form1").serialize(),function(data){
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
</script>