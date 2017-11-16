<?php $this->load->view("main/header"); ?>
<body class="gray-bg">
    <div class="wrapper wrapper-content">
        <div class="row">
        <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="post" name="form1" id="form1">
                            <div class="form-group">
		                    	<input type="file" class="form-control" style="display: none;">
								<div class="input-append input-group">
									<span class="input-group-btn">
									<button class="btn btn-white" type="button" id="insertfile">选择文件</button>
									</span>
									<input class="input-large form-control" name="file_name" id="file_name" type="text" value="">
								</div>	
                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <button class="btn btn-primary" type="button" onClick="doImport();">开始导入</button>
                                    <div id="loding"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view("main/footer"); ?>
    <link href="<?=static_url("js")?>kindeditor/themes/default/default.css" rel="stylesheet">
<script src="<?=static_url("js")?>kindeditor/kindeditor-min.js"></script>
<script src="<?=static_url("js")?>kindeditor/lang/zh_CN.js"></script>
<script>
//开始执行导入操作
function doImport(){
	var filename = $("#file_name").val();
	if (!filename){showTips('请先上传Excel文件！','error');return false;}
	$("#loding").html('<font color=red>正在导入模板，请稍后...</font>');
	$.get("<?=site_url("importfile/ImportUserExcel")?>?file="+filename,function(data){
		if (data.status == 0){
			showTips('文件导入成功！','success');
			if (data.info.length > 0){
				$("#loding").html(data.info);
			}else{
				$('#loding').html('<font color=green>完整导入，没有任何错误！</font>');
			}
		}else{
			$('#loding').html(data.info).css('color','red');
		}
	},'json');
}

var editor2;
KindEditor.ready(function(K) {
	var editor = K.editor({
		allowFileManager : true,
		uploadJson : '<?=site_url("_up/upload?dir=file")?>',
		fileManagerJson : '<?=site_url("manager/fileList?dir=file")?>',
	});
		K('#insertfile').click(function() {
			editor.loadPlugin('insertfile', function() {
				editor.plugin.fileDialog({
					clickFn : function(url) {
						K('#file_name').val(url);
						editor.hideDialog();;
					}
				});
			});
		});
});
</script>

