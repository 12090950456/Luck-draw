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
    <div id="tb" style="padding:3px">
        <span>标题:</span>
        <input id="title" style="line-height:26px;border:1px solid #ccc">
        <input type="button" value="搜索"  onclick="doSearch()">
    </div>
<table id="tt" class="easyui-datagrid"
    data-options="url:'/dxhd/admin.php/Home/User/u_record_List',fitColumns:true,singleSelect:true">
    <thead>
		<tr>
			<th data-options="field:'user_name',width:100">用户名称</th>
			<th data-options="field:'ipone',width:100">手机号码</th>
			<th data-options="field:'address',width:100">配送地址</th>
			<th data-options="field:'title',width:100">所选奖品</th>
			<th data-options="field:'addtime',width:100">录入时间</th>
		</tr>
    </thead>
</table> 
  </form>
 </body>
 <script type="text/javascript">
 /*条件搜索*/
 function doSearch(){
     $('#tt').datagrid('load',{
    	 title: $('#title').val()
     });
 } 
</script>
</html>