<?php $this->load->view("main/header")?>
 <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/echarts-all-3.js"></script>
      
<body class="gray-bg top-navigation">

    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
            <div class="wrapper wrapper-content">
                <div class="container">
                    <div class="row  border-bottom white-bg dashboard-header">
                    <?php if ($userinfo['role'] != -1){ ?>
                        <div class="col-sm-12">
				            <blockquote class="text-warning" style="font-size:14px">
				            	欢迎使用佣乐享信息管理平台
				                <br>您在使用过程中遇到任何问题或产生任何操作上的不便捷，请您及时反馈与我们
				                <br>关注佣乐享
				                <br>
				                <img src="<?=static_url("img")?>qrcode_ylx.jpg"  >
				            </blockquote>
				
				            <hr>
				        </div>
				        <?php }if ($userinfo['show_static'] == 1 || $userinfo['finance'] == 1 || $userinfo['role'] == -1){ ?>
                    	<div class="col-sm-12">
			                <div class="ibox float-e-margins">
			                    <div class="ibox-content">
			                    	<div class="echarts" id="top15s"></div>
			                    </div>
			                </div>
			            </div>
                    
                    	<div class="col-sm-12">
			                <div class="ibox float-e-margins">
			                    <div class="ibox-content">
			                    	<div class="echarts" id="mach15s"></div>
			                    </div>
			                </div>
			            </div>
                    <?php } ?>
                    </div>

                </div>

            </div>
            
        </div>
    </div>
<?php $this->load->view("main/footer");?>
<script>
<?php
	// 组合日期格式
	$stringdate = $string_data = '';
	asort($tops15_date);
	foreach($tops15_date as $key => $val){
		$stringdate .= "'".$val."',";
		$string_data .= "{$tops15[$val]['total']},";
	}
	$stringdate = rtrim($stringdate,',');
	$string_data = rtrim($string_data,',');
?>
option = {
	    title: {
	        text: '15天销量分布',
	        subtext: '按日期销量统计'
	    },
	    tooltip: {
	        trigger: 'axis'
	    },
	    toolbox: {
	        show: true,
	        feature: {
	            dataZoom: {
	                yAxisIndex: 'none'
	            },
	            dataView: {readOnly: false},
	            magicType: {type: ['line', 'bar']},
	            restore: {},
	            saveAsImage: {}
	        }
	    },
	    xAxis:  {
	        type: 'category',
	        boundaryGap: false,
	        data: [<?=$stringdate?>]
	    },
	    yAxis: {
	        type: 'value',
	        axisLabel: {
	            formatter: '{value} 部'
	        }
	    },
	    series: [
	        {
	            name:'当日销量',
	            type:'line',
	            data:[<?=$string_data?>],
	            markPoint: {
	                data: [
	                    {type: 'max', name: '最大'},
	                    {type: 'min', name: '最小'}
	                ]
	            },
	            markLine: {
	                data: [
	                    {type: 'average', name: '平均'}
	                ]
	            }
	        }
	    ]
	};

<?php 
	$mach_name = ''; $mach_total = 0;
	foreach($mach as $key => $val){
		$mach_name .= "'".$val['machname']."',";
		$mach_total .= $val['total'].",";
	}
	$mach_name = rtrim($mach_name,',');
	$mach_total = rtrim($mach_total,',');
?>
option2 = {
	title: {
       text: '15天机型销售前10统计',
       subtext: '按照机型统计'
	},
	toolbox: {
        show: true,
        feature: {
            dataZoom: {
                yAxisIndex: 'none'
            },
            dataView: {readOnly: false},
            magicType: {type: ['line', 'bar']},
            restore: {},
            saveAsImage: {}
        }
    },
    color: ['#3398DB'],
    tooltip : {
        trigger: 'axis',
        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
        }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis : [
        {
            type : 'category',
            data : [<?=$mach_name?>],
            axisTick: {
                alignWithLabel: true
            }
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'销量',
            type:'bar',
            barWidth: '60%',
            data:[<?=$mach_total?>]
        }
    ]
};
	
var myChart = echarts.init(document.getElementById('top15s'));
myChart.setOption(option);

var myChart2 = echarts.init(document.getElementById('mach15s'));
myChart2.setOption(option2);
</script>