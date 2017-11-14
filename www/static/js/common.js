/**
 * tips提醒
 * content:提醒内容
 * stype:success,error,info,warning
 * title:标题，可以不填写
 */
function showTips(content,stype,title, parnet = 0){
	if (!stype){stype = 'error';}
	if (!title){
		Command: toastr[stype](content);		//第一个参数的内容内容
	}else{
		Command: toastr[stype](content, title);		//第一个参数的内容内容
	}
	if (parent == 1){
		window.parent.toastr.options = {
				"closeButton": true,
				  "progressBar": true,
				  "positionClass": "toast-top-center",		//toast-top-center顶部居中 toast-top-right右上角  toast-bottom-right右下角
				  "onclick": null,
				  "showDuration": "200",
				  "hideDuration": "1000",
				  "timeOut": "2000",
				  "extendedTimeOut": "500",
				  "showEasing": "swing",
				  "hideEasing": "linear",
				  "showMethod": "fadeIn",
				  "hideMethod": "fadeOut"
		}
	}else{
		toastr.options = {
			  "closeButton": true,
			  "progressBar": true,
			  "positionClass": "toast-top-center",		//toast-top-center顶部居中 toast-top-right右上角  toast-bottom-right右下角
			  "onclick": null,
			  "showDuration": "200",
			  "hideDuration": "1000",
			  "timeOut": "2000",
			  "extendedTimeOut": "500",
			  "showEasing": "swing",
			  "hideEasing": "linear",
			  "showMethod": "fadeIn",
			  "hideMethod": "fadeOut"
			}
	}
}

/**
	弹出层操作ifream
*/
function showFarme(title,url,width,height){
	if (!url){showTips('参数错误，无法调用iFarme窗口，缺少url参数','error');return false;}
	if (!width){width = '50%';}
	if (!height){height = '50%';}
	layer.open({
        type: 2,
        title: !title ? '信息标题：' : title,
        shadeClose: true,
        shade: 0.8,
        area: [''+width+'', ''+height+''],
        content: !url ? 'http://layer.layui.com/mobile/' : url //iframe的url
        
    }); 
}

/**
	alert
*/
function sAlert(content){
	parent.layer.alert(content);
}