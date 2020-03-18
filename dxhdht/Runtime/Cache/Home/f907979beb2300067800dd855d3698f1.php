<?php if (!defined('THINK_PATH')) exit();?>   <!DOCTYPE html>
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
                <li class="list"><a href="/luyang" target="_bank"><span class="c1"></span>进入首页</a></li>
                <li class="list">
					<a onclick="AddTag('基本信息', '/index.php/Home/User/startup_info_get', 'icon279');">
                    	<span class="c3"></span>基本信息
                	</a>
                </li>
                <li class="list"><a onclick="AddTag('修改密码', '/dxhd/admin.php/Home/Index/user_passWord_save.html', 'icon279');"><span class="c5"></span>修改密码</a></li>
                <li class="list"><a href="/luyang/index.php/Home/Index/logout"><span class="c4"></span>退出系统</a></li>
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
                <span style="padding:0px 10px;border-right:1px solid #ccc;">当前账号:admin</span>
                <span style="padding:0px 10px;border-right:1px solid #ccc;">所属角色:管理员</span>
                <span style="padding:0px 10px;">当前版本Version 1.2.3 [20170919]</span>
            </text>
        </div>
    </div>
</div>
<div data-options="region:'west',collapsible:true,split:true,title:'导航菜单',width:'205px'">
    <div class="easyui-accordion" data-options="fit:true,border:false">
       		<div title='账号信息' data-options="iconCls:'icon52'">
                <ul class="easyui-tree" data-options="animate:true">
                <?php if('1' == $usertype): ?><li id="org_info_get" data-options="iconCls:'icon53',attributes:{url:'/dxhd/admin.php/Home/Index/org_info_get'}"><span>基本信息管理</span></li>
                    <li id="org_report_list" data-options="iconCls:'icon118',attributes:{url:'/dxhd/admin.php/Home/Index/org_report_all'}"><span>经济指标</span></li>
                    <li id="org_requirement_list" data-options="iconCls:'icon2',attributes:{url:'/dxhd/admin.php/Home/Index/org_requirement_all'}"><span>需求发布</span></li>
                    <li id="prove_get" data-options="iconCls:'icon24',attributes:{url:'/dxhd/admin.php/Home/Index/user_prove_get'}"><span>营业执照认证</span></li>
                <?php elseif('3' == $usertype): ?>
                    <li id="service_org_info_get" data-options="iconCls:'icon53',attributes:{url:'/dxhd/admin.php/Home/Index/service_org_info_get'}"><span>基本信息管理</span></li>
                    <li id="service_org_report_all" data-options="iconCls:'icon118',attributes:{url:'/dxhd/admin.php/Home/Index/service_org_report_all'}"><span>经济指标</span></li>
                    <li id="service_item_all" data-options="iconCls:'icon51',attributes:{url:'/dxhd/admin.php/Home/Index/service_item_all'}"><span>服务项目管理</span></li>
                    <li id="prove_get" data-options="iconCls:'icon24',attributes:{url:'/dxhd/admin.php/Home/Index/user_prove_get'}"><span>营业执照认证</span></li>
              	<?php elseif('2' == $usertype): ?>
                    <li data-options="iconCls:'icon53',attributes:{url:'/dxhd/admin.php/Home/Index/startup_info_get'}"><span>团队/个人信息</span></li>
                    <li id="originality_info_list" data-options="iconCls:'icon23',attributes:{url:'/dxhd/admin.php/Home/Index/originality_info_all'}"><span>我的创意</span></li>
                    <li id="startup_ability" data-options="iconCls:'icon22',attributes:{url:'/dxhd/admin.php/Home/Index/startup_ability_get'}"><span>我的能力</span></li>
                    <li id="myjl" data-options="iconCls:'icon20',attributes:{url:'/dxhd/admin.php/Home/Index/gojianli'}"><span>我的简历</span></li><?php endif; ?>
					<li id="user_passWord_save" data-options="iconCls:'icon128',attributes:{url:'/dxhd/admin.php/Home/Index/user_passWord_save.html'}"><span>修改密码</span></li>
                </ul>
            </div>
            <div title='管理信息' data-options="iconCls:'icon182',selected:true">
                <ul class="easyui-tree" data-options="animate:true">
                   <li data-options="iconCls:'icon187'"><span>创业基地</span>
                       <ul class="easyui-tree" data-options="animate:true,state:'closed'">
                           <li id="startup_site_rz_list" data-options="iconCls:'icon93',attributes:{url:'/dxhd/admin.php/Home/Index/startup_site_rz_all'}"><span>申请记录</span></li>
                           <?php if('3' == $usertype): ?><li id="startup_site_all" data-options="iconCls:'icon189',attributes:{url:'/dxhd/admin.php/Home/Index/startup_site_all'}"><span>我的创业基地</span></li><?php endif; ?>
                           <li data-options="iconCls:'icon31',attributes:{url:'/dxhd/admin.php/Home/Index/gostartup_site_list'}"><span>我要申请</span></li>
                       </ul>
            	   </li>
                   <li data-options="iconCls:'icon32'"><span>我的需求</span>
                       <ul class="easyui-tree" data-options="animate:true,state:'closed'">
                           <li id="user_requirement_all" data-options="iconCls:'icon93',attributes:{url:'/dxhd/admin.php/Home/Index/user_requirement_get'}"><span>免费发布信息</span></li>
	                          <?php if('2' == $usertype): ?><li id="qysjli" data-options="iconCls:'icon31',attributes:{url:'/dxhd/admin.php/Home/Index/ycfcAll'}"><span>企业事迹宣传</span></li><?php endif; ?>
                           <li id="user_requirement_all1" data-options="iconCls:'icon31',attributes:{url:'/dxhd/admin.php/Home/Index/user_requirement_all'}"><span>信息时事动态</span></li>
                       </ul>
            	   </li>      
                </ul>
            </div>
            <div title='服务申请' data-options="iconCls:'icon312'">
                <ul class="easyui-tree" data-options="animate:true">
	                <?php if('3' == $usertype): ?><li id="affairs" data-options="iconCls:'icon178',attributes:{url:'/dxhd/admin.php/Home/Index/getwt2'}"><span>我的任务</span>
	           				<ul class="easyui-tree" data-options="animate:true,state:'closed'">
	                           <li id="zxqy_service_activity_all0" data-options="iconCls:'icon93',attributes:{url:'/dxhd/admin.php/Home/Index/zxqy_service_activity_all?activiType=0'}"><span>我要处理</span></li>
	                           <li id="zxqy_service_activity_all1" data-options="iconCls:'icon31',attributes:{url:'/dxhd/admin.php/Home/Index/zxqy_service_activity_all?activiType=1'}"><span>我已处理</span></li>
	                       </ul>                    
	                    </li><?php endif; ?>
                    <li id="affairs" data-options="iconCls:'icon178',attributes:{url:'/dxhd/admin.php/Home/Index/getwt2'}"><span>我的申请记录</span>
           				<ul class="easyui-tree" data-options="animate:true,state:'closed'">
                           <li id="1service_activity0" data-options="iconCls:'icon31',attributes:{url:'/dxhd/admin.php/Home/Index/service_activity_all?activiType=0'}"><span>未处理</span></li>
                           <li id="1service_activity1" data-options="iconCls:'icon31',attributes:{url:'/dxhd/admin.php/Home/Index/service_activity_all?activiType=1'}"><span>已处理</span></li>
                       </ul>                    
                    </li>
					<li data-options="iconCls:'icon31',attributes:{url:'/dxhd/admin.php/Home/Index/goserver_all'}"><span>我要服务</span></li>                    
               </ul>
            </div>              
            <div title='问题流转' data-options="iconCls:'icon312'">
                <ul class="easyui-tree" data-options="animate:true">
                    <li id="affairs" data-options="iconCls:'icon178',attributes:{url:'/dxhd/admin.php/Home/Index/getwt2'}"><span>未回复</span></li>
                    <li id="platform" data-options="iconCls:'icon262',attributes:{url:'/dxhd/admin.php/Home/Index/getwt1'}"><span>已回复</span></li>
                    <li data-options="iconCls:'icon31',attributes:{url:'/dxhd/admin.php/Home/Index/gogotw'}"><span>我要提问</span></li>
               </ul>
            </div>  
            <?php if(1==$usertype): ?><div id="myZp" title='招聘管理' data-options="iconCls:'icon312'">
	                <ul class="easyui-tree" data-options="animate:true">
					   <li data-options="iconCls:'icon93',attributes:{url:'/dxhd/admin.php/Home/Index/GoRecruitment'}"><span>免费发布信息</span></li>
	                   <li id="xxgl" data-options="iconCls:'icon31',attributes:{url:'/dxhd/admin.php/Home/Index/qyzpAll'}"><span>招聘信息管理</span></li>
	                   <li id="jljh" data-options="iconCls:'icon31',attributes:{url:'/index.php/Home/Index/startup_site_list'}"><span>所收简历集合</span></li>
	               </ul>
	            </div><?php endif; ?>   
            <div title='设置' data-options="iconCls:'icon84'"">
                <ul class="easyui-tree" data-options="animate:true">
                    <li data-options="iconCls:'icon220',attributes:{url:'/admin.php/Consulting/Type/index'}"><span>帮助中心</span></li>
                    <li data-options="iconCls:'icon220',attributes:{url:'/dxhd/admin.php/Home/Index/about.html'}"><span>关于我们</span></li>
              </ul>
            </div>
            <div title='意见反馈' data-options="iconCls:'icon84'">
                <ul class="easyui-tree" data-options="animate:true">
                    <li data-options="iconCls:'icon220',attributes:{url:'/dxhd/admin.php/Home/Index/user_opinion.html'}"><span>意见反馈</span></li>
              </ul>
            </div>            
         </div>

</div>
<div data-options="region:'center'">
    <div id="MTabs" data-options="fit:true,tabHeight:32,scrollIncrement:200,border:false" class="easyui-tabs"></div>
</div>
<script src="/dxhd/Public/dxhdht/left/js/date.js"></script>
<script>
	$(".userInfo div").each(function(){
		$(this).mouseover(function(){
			$(this).children("div").show();
		});
		$(this).mouseout(function(){
			$(this).children("div").hide();
		});			
	});
</script>
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

        AddTag("基本信息", "", "icon279");

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
    function LoginOut() {
        $.post("/admin.php/System/Login/logout.html", function (data) {
            if (data.result) {
                top.location.href = data.des;
            }
        }, "json");
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