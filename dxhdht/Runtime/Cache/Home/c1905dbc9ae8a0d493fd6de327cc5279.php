<?php if (!defined('THINK_PATH')) exit();?><html>
 <head> 
  <title>Index</title> 
  <meta http-equiv="”X-UA-Compatible”" content="”IE=edge,chrome=1″" /> 
  <meta name="viewport" content="width=device-width" /> 
  <meta name="renderer" content="webkit|ie-comp|ie-stand" /> 
    <link href="/dxhd/Public/dxhdht/left/css/easyui.css" rel="stylesheet" />
    <link href="/dxhd/Public/dxhdht/left/css/Main.css" rel="stylesheet" />
    <script src="/dxhd/Public/dxhdht/left/js/jquery.min.js"></script>
    <script src="/dxhd/Public/dxhdht/left/js/jquery.easyui.min.js"></script>
    <script src="/dxhd/Public/dxhdht/left/js/XB.js"></script>
    <link href="/dxhd/Public/dxhdht/left/css/font-awesome.min.css" rel="stylesheet">
 </head> 
 <body class="Bodybg"> 
  <form id="FF" method="post"> 
 <div id="tools" class="tools"> 
    <a href="javascript:void(0);" class="ToolBtn" onclick="$('#tt').datagrid('reload'); "><span class="icon6"></span><b>刷新</b></a> 
   </div> 
<table id="tt" class="easyui-datagrid"
    data-options="url:'/dxhd/admin.php/Home/User/prizeListc',fitColumns:true,singleSelect:true">
    <thead>
		<tr>
			<th data-options="field:'title',width:100">奖品名称</th>
			<th data-options="field:'day_count',width:100">每天发放剩余</th>
			<th data-options="field:'a_count',width:100">已发放数量</th>
			<th data-options="field:'s_count',width:100">剩余数量</th>
			<th data-options="field:'count',width:100">总数量</th>
		</tr>
    </thead>
</table> 
  </form>
 </body>
</html>