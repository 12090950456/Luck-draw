<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title>Index</title>
    <meta http-equiv=”X-UA-Compatible” content=”IE=edge,chrome=1″ />
    <meta name="viewport" content="width=device-width" />
    <meta name="renderer" content="webkit|ie-comp|ie-stand" />
    <link href="/dxhd/Public/dxhdht/left/css/easyui.css" rel="stylesheet" />
    <link href="/dxhd/Public/dxhdht/left/css/Main.css" rel="stylesheet" />
    <script src="/dxhd/Public/dxhdht/left/js/jquery.min.js"></script>
    <script src="/dxhd/Public/dxhdht/left/js/jquery.easyui.min.js"></script>
    <script src="/dxhd/Public/dxhdht/left/js/XB.js"></script>
    <link href="/dxhd/Public/dxhdht/left/css/font-awesome.min.css" rel="stylesheet">
    <link href="/dxhd/Public/dxhdht/left/css/wadd.css" rel="stylesheet">
</head>
<body class="easyui-layout Father">
<div data-options="region:'north',border:false,minWidth:1000">
    <div class="Top">
        <div class="Logo">
            <img src="/dxhd/Public/dxhdht/left/images/logo.png" alt="CMS" />
        </div>
        <div id="TopRight" class="TopRight">
            <ul id="topnav">
           		<li class="list"><a onclick="addNews()"><span class="c5"></span>修改密码</a></li>
                <li class="list"><a href="/dxhd/admin.php/Home/User/logout"><span class="c4"></span>退出系统</a></li>
            </ul>
        </div>
    </div>
</div>
<div data-options="region:'south',border:false,height:'20px',minWidth:1000">
    <div class="bottomBorder">
        <div class="footer" style="float:left; margin-left: 5px;"><span class="shus"></span>
            <span class="shu"></span>
            <span id="TopDate1"></span>
            <span id="TopDate2"></span>
        </div>
        <div class="footer" style="float:right; text-align: right; margin-right:5px;">
            <text>
                <span style="padding:0px 10px;border-right:1px solid #ccc;">Copyright &copy; 2017 - 2019</span>
                <span style="padding:0px 10px;border-right:1px solid #ccc;">当前账号:<?php echo ($username); ?></span>
                <span style="padding:0px 10px;border-right:1px solid #ccc;">所属角色:<?php echo ($uname); ?></span>
                <span style="padding:0px 10px;">当前版本Version 1.2.3 [20170919]</span>
            </text>
        </div>
    </div>
</div>
<div data-options="region:'west',collapsible:true,split:true,title:'导航菜单',width:'205px'">
    <div class="easyui-accordion" data-options="fit:true,border:false">
   		<div title='账号信息' data-options="iconCls:'icon52'">
            <ul class="easyui-tree" data-options="animate:true">
                <li data-options="iconCls:'icon53',attributes:{url:'/dxhd/admin.php/Home/User/u_recordlist'}"><span>得奖用户集合</span></li>
                <li data-options="iconCls:'icon53',attributes:{url:'/dxhd/admin.php/Home/User/prizelist'}"><span>奖品信息集合</span></li>
            </ul>
        </div>
    </div>

</div>
<div data-options="region:'center'">
    <div id="MTabs" data-options="fit:true,tabHeight:32,scrollIncrement:200,border:false" class="easyui-tabs"></div>
</div>
  <div id="enditTab" class="easyui-dialog" style="width: 400px;" closed="true" >
    <form action="/dxhd/admin.php/Home/User/save_ps" method="post" id="saveform">
	    <table class="EditTable"">
	      <thead>
				<tr>
		           <td colspan="2">说明：带<span class="Red">*</span>必填；</td>
		           <input type="hidden" id="xxid" name="xxid"/>
		        </tr>
	        </thead>    
	      <tbody> 
	       <tr> 
	        <td align="right" width="200"><span class="Red">*</span> 输入新密码：</td> 
	        <td> 
	        	<input type="password" name="password" id="password"/>
	        </td> 
	       </tr> 
	       <tr> 
	        <td align="right" width="200"><span class="Red">*</span> 请再次输入新密码：</td> 
	        <td> 
	        	<input type="password" name="npassword" id="npassword"/>
	        </td> 
	       </tr>	       
			<tr>  
			  <td colspan="2">
				<div class="dialog-button">
					<a href="javascript:savePs()" class="l-btn l-btn-small" group="" id="">
						<span class="l-btn-left l-btn-icon-left">
							<span class="l-btn-text"> 确 定 </span>
							<span class="l-btn-icon icon-ok">&nbsp;</span>
						</span>
					</a>
					<a href="javascript:$('#enditTab').dialog('close')" class="l-btn l-btn-small" group="" id="">
						<span class="l-btn-left l-btn-icon-left">
							<span class="l-btn-text"> 取 消 </span>
							<span class="l-btn-icon icon-back">&nbsp;</span>
						</span>
					</a>
				</div>
				</td>                 
	       </tr>         
	      </tbody> 
	     </table>	
     </form>
  </div>
<script src="/dxhd/Public/dxhdht/left/js/date.js"></script>
<script>
    function OpenWin(Type) {
        switch (Type) {
            case 'modifypwd':$.XB.open({ 'type':'add','openmode':'0', 'dialog': { 'url': 'admin.php/System/Index/modifypwd/', 'title': '修改密码' } });
                break;

        }}
    $(function () {
        var TopDate = $("#TopDate1");
        showDate(TopDate);
        TopDate = $("#TopDate2");
        setInterval(function () { showTime(TopDate); }, 1000);

        AddTag("得奖用户集合", "/dxhd/admin.php/Home/User/u_recordlist", "icon279");

        $('.easyui-tree').tree({
            onClick: function (node) {
                if (typeof (node.attributes) != "undefined") {
                    AddTag(node.text, node.attributes.url, node.iconCls);
                }
            }
        });

    });
    function AddTag(title, url, icon) {
        if ($("#MTabs").tabs("exists", title)) {
            $('#MTabs').tabs('update', {
                tab: $('#MTabs').tabs('getTab', title),
                options: {
                    content: '<iframe name="iframe" src="' + url + '" width="100%" height="100%" frameborder="0" scrolling="yes"></iframe>'
                }
            }).tabs('select', title);
        }
        else {
            $('#MTabs').tabs('add', {
                title: title,
                content: '<iframe name="iframe" src="' + url + '" width="100%" height="100%" frameborder="0" scrolling="yes"></iframe>',
                closable: true,
                selected: true,
                iconCls: icon,
                bodyCls: 'NoScroll'
            });
            TagMenu();
        }
    }
    function TagMenu() {
        /*为选项卡绑定右键*/
        $(".tabs li").on('contextmenu', function (e) {
            /*选中当前触发事件的选项卡 */
            var subtitle = $(this).text();
            $('#MTabs').tabs('select', subtitle);
            //显示快捷菜单
            $('#tab_menu').menu('show', {
                left: e.pageX,
                top: e.pageY
            }).menu({
                onClick: function (item) {
                    closeTab(item.id);
                }
            });

            return false;
        });
        $(".tabs-inner").dblclick(function () {
            var subtitle = $(this).children("span").text();
            $('#MTabs').tabs('close', subtitle);
        })
    }
    function closeTab(action) {
        var alltabs = $('#MTabs').tabs('tabs');
        var currentTab = $('#MTabs').tabs('getSelected');
        var allTabtitle = [];
        $.each(alltabs, function (i, n) {
            allTabtitle.push($(n).panel('options').title);
        })
        switch (action) {
            case "refresh":
                var iframe = $(currentTab.panel('options').content);
                var src = iframe.attr("src");
                $('#MTabs').tabs('update', {
                    tab: currentTab,
                    options: {
                        content: '<iframe name="iframe" src="' + src + '" width="100%" height="100%" frameborder="0" scrolling="yes"></iframe>'
                    }
                })
                break;
            case "close":
                var currtab_title = currentTab.panel('options').title;
                $('#MTabs').tabs('close', currtab_title);
                break;
            case "closeall":
                $.each(allTabtitle, function (i, n) {
                    $('#MTabs').tabs('close', n);
                });
                break;
            case "closeother":
                var currtab_title = currentTab.panel('options').title;
                $.each(allTabtitle, function (i, n) {
                    if (n != currtab_title) {
                        $('#MTabs').tabs('close', n);
                    }
                });
                break;
            case "closeright":
                var tabIndex = $('#MTabs').tabs('getTabIndex', currentTab);
                $.each(allTabtitle, function (i, n) {
                    if (i > tabIndex) {
                        $('#MTabs').tabs('close', n);
                    }
                });
                break;
            case "closeleft":
                var tabIndex = $('#MTabs').tabs('getTabIndex', currentTab);
                $.each(allTabtitle, function (i, n) {
                    if (i < tabIndex) {
                        $('#MTabs').tabs('close', n);
                    }
                });
                break;
            case "exit":
                $('#tab_menu').menu('hide');
                break;
        }
    }
</script>
  <script>
  /*显示密码修改*/
  function addNews(){
 	 $('#saveform')[0].reset();
 	 $("#enditTab").dialog("open").dialog('setTitle', '密码修改');
  }
  function savePs(){
	  var ps=$("#password").val();
	  var nps=$("#npassword").val();
	  if(ps){
		  if(ps!=nps){
			  alert("两次密码输入不一致！");
			  return false;
		  }else{
			  $('#saveform').submit();
		  }
	  }else{
		  alert("请输入新密码！");
		  return false;
	  }
  }
  </script>
<div id="tab_menu" class="easyui-menu" style="width: 150px;display:none;">
    <div id="refresh">刷新标签</div>
    <div class="menu-sep"></div>
    <div id="close">关闭标签</div>
    <div id="closeall">全部关闭</div>
    <div id="closeother">关闭其他</div>
    <div class="menu-sep"></div>
    <div id="closeright">关闭右边</div>
    <div id="closeleft">关闭左边</div>
    <div class="menu-sep"></div>
    <div id="exit">退出菜单</div>
</div>

</body>
</html>