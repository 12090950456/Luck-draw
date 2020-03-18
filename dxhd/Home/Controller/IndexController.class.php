<?php
namespace Home\Controller;
use Think\Controller;
use Think\JSSDK;
use Think\Encrypt;
class IndexController extends Controller {
   private  $appid="wx0974edb757b9c52f";
   private  $appSecret ="29d95b9446e7645ddb11790eb9e67040";
    public function index(){
        /*
         * 改动1.首页2.给好友翻牌页面3.领奖页面 其中的任何一个后台。其他的响应改变
         * 
         * 
         * 只有我自己的翻牌的页面中才会有分享页面的按钮。这个时候连接是没有任何ID的。所以只能通过微信分享接口去做分享的功能。
         * 通过微信自带的分享都是分享的翻牌初始连接。
         * 
         * 如果他是通过微信自带的分享怎么办？
         * 我要一开始就要加上openid;
         * 如果是通过我写的脚本分享。那就我自己组装分享的连接。
         * 我的ID 无论何时都要有，因为他任何操作都要用到。好友的ID只有在帮他翻牌的时候才用到。
         * 如果，好友的ID存在了，我的没存在，那么在获取我的ID的时候好友的ID就没了。但是如果用session存储
         * 好友的ID的话那么这个好友的ID就一直存在了。
         * 所以如果是为好友翻牌的话那么自己的ID就不用要。如果在好友的翻牌页面的话。想要自己翻拍那么单独给一个进入翻牌的初始连接。
         * */
            $redirect_uri="http://www.51wxwx.com/weiObject/dxhd";
            if(isset($_GET["code"])){
                $openid=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appid&secret=$this->appSecret&code=".$_GET["code"]."&grant_type=authorization_code");
                $openid=json_decode($openid);
                if(isset($openid->errcode)){
                    echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                    exit;
                }else{
                    $userinfo=file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=".$openid->access_token."&openid=".$openid->openid);
                    if($userinfo){
                        $userinfo=json_decode($userinfo);
                        $wxuser=M("u_record")->where("user_id='".$openid->openid."'")->find();
                        if(!$wxuser){
                            $data["user_id"]=$openid->openid;
                            $data["wxuser_name"]=$userinfo->nickname;
                            $data["use_count"]=1;
                            M("u_record")->add($data);
                        }
                        $this->assign("headimg",$userinfo->headimgurl);
                    }
                    /**
                     * 在帮自己翻牌里面才会有分享的按钮。
                     * 1.微信appid 
                     * 2.时间戳 
                     * 3.随机字符串  
                     * 4.签名
                     * 拿到签名的步骤：
                     * 4.1 拿到access_token
                     * 4.2 拿到jsapi_ticket（有效期7200秒，开发者必须在自己的服务全局缓存jsapi_ticket）
                     * 4.3 生成签名
                     */
                    $jssdk = new JSSDK("$this->appid", "$this->appSecret");
                    $signPackage = $jssdk->GetSignPackage();
                    $this->assign("signPackage",$signPackage);
                    $this->assign("userid",$openid->openid);
                    //获取所有的卡片
                    $cardList=M("card")->select();
                    //获取已拥有的卡片和这张卡片的数量
                    $sql1="SELECT card_id,SUM(coun) AS coun FROM (SELECT card_id,COUNT(1) AS coun FROM uf_record WHERE 
                        fuser_id='".$openid->openid."' GROUP BY card_id) AS atab GROUP BY atab.card_id";
                    $cardnum=M("")->query($sql1);
                    foreach ($cardList as $key => $value) {
                        foreach ($cardnum as $key1 => $value1) {
                           if($value1["card_id"]==$value["card_id"]){
                               $value["num"]=$value1["coun"];
                           }
                        }
                        $cardList[$key]=$value;
                    }
                    $this->assign("cardList",$cardList); //把所有的卡片和我拥有的卡片数据组合下
                    //获取已领奖的客户字段（u_record）/手机号码/手机号码不为空的count()/u_record和uf_record数据的总和/
                   
                    //$urecord所有手机号码
                    $urecord=M("u_record")->where("ipone!=''")->select();
                    foreach ($urecord as $key => $value) {
                        $pattern = "/(\d{3})\d{4}(\d{4})/";
                        $replacement = "\$1****\$2";
                        $ipone1= preg_replace($pattern, $replacement, $value["ipone"]);
                        $value["ipone"]=$ipone1;
                        $urecord[$key]=$value;
                    }
                    $this->assign("urecord",$urecord);
                    //带翻牌x
                    $this->assign("ufEcord",count(M("uf_record")->where("fuser_id='".$openid->openid."'")->select()));
                    //已翻牌
                    $u_record=M("u_record")->join("left join prize on u_record.prize_id=prize.prize_id")->where("user_id='".$openid->openid."'")->find();
                    $this->assign("uEcord",$u_record?1:0);
                    $this->assign("u_record",$u_record);
                    //u_recordC人已经兑换奖品
                    $u_recordC=count($urecord);
                    $this->assign("u_recordC",$u_recordC);
                    //总共x人参与
                    $dfsql="SELECT * FROM(SELECT * FROM uf_record WHERE fuser_id=user_id) AS gu GROUP BY gu.user_id";
                    $ufcount=count(M("uf_record")->query($dfsql));
                    $ucount=count(M("u_record")->select());
                    $this->assign("tcount",$ufcount+$ucount);
                    //查询有多少个不同样子的牌子/用于验证是否可以领取奖品
                    $sql="SELECT * FROM (SELECT card_id FROM uf_record where fuser_id='".$openid->openid."' GROUP BY card_id) AS atab GROUP BY card_id";
                    $fuc=M("u_record")->query($sql);
                    $this->assign("fuc",count($fuc));
                    //输出所有奖品
                    $this->assign("prizeList",M("prize")->limit("7")->select()); 
                    //查看是否有翻牌次数
                    $this->assign("usecount",M("u_record")->where("use_count>0 and user_id='".$openid->openid."'")->find());
                    //输出加密openid
                    $this->assign("openid",$this->encode($openid->openid));
                    $this->display("index");
                }
            }else{
                echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                exit;
            }            
    }
        /**
         * 函数用途描述:
         * 下午12:55:30
         * 作者：魏博宇
         */
        public function u_recordSOrder(){
            try {       
                $data["is_order"]=0;
                $res=M("u_record")->where("user_id='".I("openid")."'")->save($data);
                if($res>0){
                    echo 1;
                }else{
                    echo 0;
                }
            } catch (\Exception $e) {
                echo "0";
            }
        }
    /**进入翻牌页面入口
     * 函数用途描述:返回opendi
     *2018年4月15日 下午12:33:02
     * 作者：魏博宇
     */
    public function go_F(){
        try {
        /*
         * 限制帮好友分享次数限制：1.可以帮多个好友翻牌，但是每个好友只能翻一次
         * 首先我该如何限制：1.我的翻牌记录一个表2.好友的翻牌记录一个帮3.如果是好友帮别人翻牌的话，那就查询好友翻牌表，
         * 如果有数据满足了以下条件说明这个好友已经帮我翻过牌子了：我的ID(userid)和好友的ID(fuserid)都在一条记录当中。
         * 
         * 做法：
         * 限制好友分享限制思路和做法(FID=好友连接，UID=我的连接)
         * F页面：收到一个带有FID的连接
         * 步骤1：点击这个连接的时候用session存储FID
         * 步骤2：通过里面的按钮进入帮好友翻牌页面的时候获取UID(因为是同一个域名下所以现在FID是存在的)
         * (获取完成之后也可以查询一下是否有数据同时包含FID和UID的，如果有就提示已经翻过牌了，没有就进入操作页面)
         * 步骤3：点击帮好友翻牌的时候进入代码查询又没有数据同时包含FID和UID的。如果有就提示已经翻过牌了
         * 没有进给好友翻牌同时新增数据。
         * */
            /**
             * 会不会出现，好友和自己的ID同时存在？
             * 首先。用户第一次进入html页面的时候。只有自己的ID。翻完牌后，分享一个带有好友ID（/也就是我的ID/我对于别人来说就是好友/也就是自己的ID“fuserid=userid”）
             * 并且这个分享的连接是没有自己的ID的（也就是userid/本处是你好友的ID所以这个userid=“空的”）的连接。
             * 好友点击连接进入index发现没有 userid但是fuserid是存在的，所以会进入if($fuserid)，之后在进入html，在里面有给我自己翻牌的按钮
             * 点击进入翻牌的初始连接。初始页面在获取自己的ID。
             * 。所以是不存在好友的ID和自己的ID同事存在的。
             */
            $fuserid=I("fuserid");
            $fuserid1=$_SESSION["fuserid"];
            //$fuserid如果这个存在说明是第一次进入然后就会刷新页面
            if($fuserid){
                $_SESSION["fuserid"]=$fuserid;
                $_SESSION["tx"]=I("tx");
            }elseif($fuserid1){
                //进入这个方法说明已经得到了东西
            }else{
                $this->wby_alerturl("打开方式不正确","index");
            }
            $redirect_uri="http://www.51wxwx.com/weiObject/dxhd/index.php/home/Index/go_F";
            if(isset($_GET["code"])){
                $openid=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appid&secret=$this->appSecret&code=".$_GET["code"]."&grant_type=authorization_code");
                $openid=json_decode($openid);
                if(isset($openid->errcode)){
                    echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                    exit;
                }else{
                    //查看是否已经帮这个好友翻过牌子/如果翻过牌子就显示这个牌子
                    $fuserid=$_SESSION["fuserid"];
                    $this->assign("fuserid",$fuserid);
                    $this->assign("userid",$openid->openid);
                    $this->assign("tx",$_SESSION["tx"]);
                    $this->display("help");
                }
            }else{
                echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                exit;
            }  
        } catch (\Exception $e) {
            $this->wby_alerturl("打开方式不正确","index");
        }
    }
    /**
     * 函数用途描述:进入翻牌页面
     *2018年4月15日 下午2:14:13
     * 作者：魏博宇
     */
    public function fp(){
        try {
            $redirect_uri="http://www.51wxwx.com/weiObject/dxhd/index.php/home/Index/fp";
            if(isset($_GET["code"])){
                $openid=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appid&secret=$this->appSecret&code=".$_GET["code"]."&grant_type=authorization_code");
                $openid=json_decode($openid);
                if(isset($openid->errcode)){
                    echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                    exit;
                }else{
                    $userid=$openid->openid;
                    $userinfo=file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=".$openid->access_token."&openid=".$openid->openid);
                     if($userinfo){
                        $userinfo=json_decode($userinfo);
                        $this->assign("headimg",$userinfo->headimgurl);
                     }
                    
                    //$urecord所有手机号码
                    $urecord=M("u_record")->where("ipone!=''")->select();
                    foreach ($urecord as $key => $value) {
                        $pattern = "/(\d{3})\d{4}(\d{4})/";
                        $replacement = "\$1****\$2";
                        $ipone1= preg_replace($pattern, $replacement, $value["ipone"]);
                        $value["ipone"]=$ipone1;
                        $urecord[$key]=$value;
                    }
                    $this->assign("urecord",$urecord);
                    //带翻牌x
                    $this->assign("ufEcord",count(M("uf_record")->where("fuser_id='".$userid."'")->select()));
                    //已翻牌
                    $u_record=count(M("u_record")->where("user_id='".$userid."'")->find());
                    $this->assign("uEcord",$u_record?1:0);
                    //u_recordC人已经兑换奖品
                    $u_recordC=count($urecord);
                    $this->assign("u_recordC",$u_recordC);
                    //总共x人参与
                    $dfsql="SELECT * FROM(SELECT * FROM uf_record WHERE fuser_id=user_id) AS gu GROUP BY gu.user_id";
                    $ufcount=count(M("uf_record")->query($dfsql));
                    $ucount=count(M("u_record")->select());
                    $this->assign("tcount",$ufcount+$ucount);
                    
                    //查看是否已经帮这个好友翻过牌子/如果翻过牌子就显示这个牌子                    
                    $fuserid=$_SESSION["fuserid"];
                    $ufData=M("uf_record")->field("card.card_id,card.title")->join("left join card on uf_record.card_id=card.card_id")->where("user_id='$userid' and fuser_id='$fuserid'")->find();
                    if($ufData){
                        //已经翻过牌子了
                        $this->assign("ufData",$ufData);   
                    }
                    $this->assign("userid",$userid);
                    $this->display("friend");
                }
            }else{
                echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                exit;
            }
        } catch (\Exception $e) {
            $this->wby_alerturl("打开方式不正确","index");
        }
    }
    /**
     * 函数用途描述:好友帮忙翻牌子
     *2018年4月15日 下午2:24:10
     * 作者：魏博宇
     */
    public function fxstart(){
        try {
            //这个页面测试下需不需要加一个wx分享限制
            /** 
             * 1.$fuserid和$userid都不允许为空
             * 2.这里要判断fuserid和userid是否一样，防止自己给自己刷票
             * 3.要判断是否已经帮助这个好友翻过牌子了
             * 4.如果是用户自己翻牌子的话那么这个时候是没有好友的ID的所以可以直接断定：如果如果今天新发的牌子已经发完了。
             * 那么可以直接根据fuserID查询用户已经有的牌子然后给他             
             */
            $fuserid=$_SESSION["fuserid"];
            $userid=I("userid");
            //第一步
            if($userid&$fuserid){
                //第二步
                if($fuserid==$userid){
                    echo "-2";  //不允许自己给自己刷票
                }else{
                    //第三步
                    $isCard=M("uf_record")->where("user_id='$userid' and fuser_id='$fuserid'")->find();
                    if ($isCard){
                        //已经翻过了就显示翻过的牌子
                        echo "-3";
                    }else{
                        //第四步  没翻过牌子->开始翻牌子
                        /**
                         * 随即一个很大的数字，每个卡片都有两个数字(最大和最小)，随即的数字只要在两个数字之间或者等于这两个数字中的任何一个
                         * 就可以得到这个卡片 目前8位数
                         */
                        //获取所有开启并且还有剩余牌子数据
                        $cardList=M("card")->where("day_count>0 and is_open=1")->select();
                        $card=array();
                        //10000000 设置数据库最大数字
                        $xx=rand(1000000,2000000);
                        foreach ($cardList as $key => $value) {
                            if($xx<=$value["probability_m"]&&$xx>=$value["probability_s"]){
                                $card["cimg"]=$value["img_url"];
                                $card["card_id"]=$value["card_id"];
                            }
                        }
                        //如果没有数据说明:今天新发放出来的牌子已经发完了。那就给用户一个已经有的牌子
                        if(!$card){
                            //查询的是好友翻拍记录：用随机数随机派送已有的牌子
                            $fcord=M("uf_record")->field("uf_record.card_id,card.img_url")->join("left join card on uf_record.card_id=card.card_id")->where("uf_record.fuser_id='$fuserid'")->select();
                            if($fcord){
                                $ran=rand(0,count($fcord)-1);
                                $card["cimg"]=$fcord[$ran]["img_url"];
                                $card["card_id"]=$fcord[$ran]["card_id"];
                            }else{
                                $card["cimg"]="6.png";
                                $card["card_id"]="6";                                
                            }

                        }
                        //新增好友翻牌数据
                        $data["user_id"]=$userid;
                        $data["fuser_id"]=$fuserid;
                        $data["card_id"]=$card["card_id"];
                        $res=M("uf_record")->add($data);
                        //在这里减少牌子1.每天发放的数量2.发出总数量3.剩余总数量
                        $this->card_save($card["card_id"]);
                        print_r(json_encode($card));
                    }
                }
            }else{
                echo "-1";
            }
        } catch (\Exception $e) {
            echo "-1";
        }
    }
    /**
     * 函数用途描述:用户翻牌子
     *2018年4月15日 下午2:24:10
     * 作者：魏博宇
     */
    public function uxstart(){
        try {
            /**
             * 1.$userid存在并且use_count翻牌次数=0不允许为空
             * 2.判断是否已经翻过牌子了
             * 3.如果没翻过新增数据否则提示已经翻过牌子了
             */
            $userid=I("userid");
            //第一步
            if(!$userid){
                echo "-1";
            }else{
                //第二步
                $urecord=M("u_record")->where("user_id='$userid'")->find();
                if($urecord){
                    $urecordOK=M("u_record")->where("user_id='$userid' and use_count>0")->find();
                    if($urecordOK){
                        //获取所有开启并且还有剩余牌子数据
                        $cardList=M("card")->where("day_count>0 and is_open=1")->select();
                        $card=array();
                        //10000000 设置数据库最大数字
                        $xx=rand(1000000,2000000);
                        foreach ($cardList as $key => $value) {
                            if($xx<=$value["probability_m"]&&$xx>=$value["probability_s"]){
                                $card["title"]=$value["title"];
                                $card["card_id"]=$value["card_id"];
                            }
                        }
                        //如果没有数据说明:今天新发放出来的牌子已经发完了。那就给用户一个已经有的牌子
                        if(!$card){
                            //查询的是好友翻拍记录：用随机数随机派送已有的牌子
                            $fcord=M("uf_record")->field("uf_record.card_id,card.img_url")->join("left join card on uf_record.card_id=card.card_id")->where("uf_record.fuser_id='$userid'")->select();
                            if($fcord){
                                $ran=rand(0,count($fcord)-1);
                                $card["title"]=$fcord[$ran]["title"];
                                $card["card_id"]=$fcord[$ran]["card_id"];
                            }else{
                                $card["title"]="莱万";
                                $card["card_id"]="6";
                            }
                        
                        }
                        //新增好友翻牌数据
                        $data["user_id"]=$userid;
                        $data["fuser_id"]=$userid;
                        $data["card_id"]=$card["card_id"];
                        M("uf_record")->add($data);
                        //翻完牌子后这个牌子的次数-1
                        $this->card_save($card["card_id"]);
                        //翻完牌子后用户的翻牌次数-1
                        $udata["use_count"]=--$urecordOK["use_count"];
                        M("u_record")->where("user_id='$userid'")->save($udata);
                        print_r(json_encode($card));
                    }else{
                        echo "-2";
                    }
                }else{
                    //第三步
                    //获取所有开启并且还有剩余牌子数据
                    $cardList=M("card")->where("day_count>0 and is_open=1")->select();
                    $card=array();
                    //10000000 设置数据库最大数字
                    $xx=rand(1000000,2000000);
                    foreach ($cardList as $key => $value) {
                        if($xx>=$value["probability_s"]&&$xx<=$value["probability_m"]){
                            $card["title"]=$value["title"];
                            $card["card_id"]=$value["card_id"];
                        }
                    }
                    if(!$card){
                        $card["title"]="莱万";
                        $card["card_id"]="6";
                    }
                    //新增用户翻牌数据
                    $data["user_id"]=$userid;
                    $data["card_id"]=$card["card_id"];
                    M("u_record")->add($data);
                    //翻完牌子后这个牌子的次数-1
                    $this->card_save($card["card_id"]);
                    print_r(json_encode($card));
                }
            }
        } catch (\Exception $e) {
            echo "-1";
        }
    }
        /**
         * 函数用途描述:奖品领取开始
         * 上午10:00:45
         * 作者：魏博宇
         */
        public function get_card(){
            try {
                /*
                 * 修改语句：UPDATE dxhd.card SET day_count = '1' , probability_s = '1' , probability_m = '2'  WHERE card_id = 1
                 * 1.查看奖项是否已经领取过了
                 * 2.奖项兑换的次数每天都有指定的限制。每天的领取次数领完之后，就提醒已经领完，请明天再来
                 */
                $user=I("userid");
                //第一步
                $obj=M("u_record")->where("user_id='$user' and is_true=1")->find();
                if(!$obj){
                    //第二部
                    $prize_id=I("prize_id");
                    $pobj=M("prize")->where("day_count>0 and s_count>0 and prize_id=".$prize_id)->find();
                    if($pobj){
                        $data["prize_id"]=$prize_id;
                        $data["user_name"]=I("user_name");
                        $data["ipone"]=I("ipone");
                        $data["is_true"]="1";
                        $data["address"]=I("address");
                        $res=M("u_record")->where("user_id='".$user."'")->save($data);
                        if($prize_id==10||$prize_id==11||$prize_id==12){
                            $this->prize_save("7");
                        }
                        $this->prize_save($prize_id);
                        echo $res;
                    }else{
                        echo "-2";
                    }
                }else{
                    echo "-1";
                }
            } catch (\Exception $e) {
                echo 0;
            }
        }
            /**
             * 函数用途描述:减少卡牌1.每天发放的数量2.发出总数量3.剩余总数量
             * 下午12:30:17
             * 作者：魏博宇
             */
            public function card_save($cardid=""){
                try {       
                    if($cardid){
                        $card=M("card")->find($cardid);
                        $day_count=$card["day_count"];
                        $a_count=$card["a_count"];
                        $s_count=$card["s_count"];
                        $data["card_id"]=$cardid;
                        $data["day_count"]=--$day_count;
                        $data["a_count"]=++$a_count;
                        $data["s_count"]=--$s_count;
                        M("card")->save($data);
                    }
                } catch (\Exception $e) {
                    $this->wby_alert("出现异常!");
                }
            }
            /**
             * 函数用途描述:减少奖品1.每天发放的数量2.发出总数量3.剩余总数量
             * 下午12:30:17
             * 作者：魏博宇
             */
            public function prize_save($prizeid=""){
                try {
                    if($prizeid){
                        $card=M("prize")->find($prizeid);
                        $day_count=$card["day_count"];
                        $a_count=$card["a_count"];
                        $s_count=$card["s_count"];
                        $data["prize_id"]=$prizeid;
                        $data["day_count"]=--$day_count;
                        $data["a_count"]=++$a_count;
                        $data["s_count"]=--$s_count;
                        M("prize")->save($data);
                    }
                } catch (\Exception $e) {
                    $this->wby_alert("出现异常!");
                }
            }
            /**
             * 函数用途描述:奖品领取开始
             * 上午10:00:45
             * 作者：魏博宇
             */
            public function prize_savew(){
                try {
                    /*
                     * 修改语句：UPDATE dxhd.card SET day_count = '1' , probability_s = '1' , probability_m = '2'  WHERE card_id = 1
                     * 1.查看奖项是否已经领取过了
                     * 2.奖项兑换的次数每天都有指定的限制。每天的领取次数领完之后，就提醒已经领完，请明天再来
                     */
                    $user=I("userid");
                    //验证是否抽过奖品红包的奖品
                    //第一步
                    $obj=M("u_record")->where("user_id='$user' and is_true=1")->find();
                    if(!$obj){
                        //第二部
                        $prize_id=I("prize_id");
                        $pobj=M("prize")->where("day_count>0 and s_count>0 and prize_id=".$prize_id)->find();
                        if($pobj){
                            $productn="";
                            //如果用户选择的奖品是红包的话，先查询是否抽取过红包了，如果抽取过红包了就直接显示抽取的红包
                            if($prize_id==7){
                                $sql="SELECT * FROM(SELECT * FROM `u_record` WHERE prize_id=10 OR prize_id=11 OR prize_id=12 ) AS u WHERE  u.user_id='$user'";
                                $redu=M("u_record")->query($sql);
                                if($redu[0]){
                                    if($redu[0]["prize_id"]==10){
                                        print_r(json_encode(array("res"=>"1","productn"=>"3元红包")));
                                        exit;
                                    }elseif ($redu[0]["prize_id"]==11){
                                        print_r(json_encode(array("res"=>"1","productn"=>"5元红包")));
                                        exit;
                                    }elseif ($redu[0]["prize_id"]==12){
                                        print_r(json_encode(array("res"=>"1","productn"=>"10元红包")));
                                        exit;
                                    }
                                }
                                $rend=rand(0,1850);
                                if($rend>=300){
                                    $prize_id=10;
                                    $productn="3元红包";
                                }elseif ($rend>=100&&$rend<=299){
                                    $prize_id=11;
                                    $productn="5元红包";
                                }elseif ($rend<99){
                                    $prize_id=12;
                                    $productn="10元红包";
                                }else{
                                    $prize_id=10;
                                    $productn="3元红包";
                                }
                            }
                            $data["prize_id"]=$prize_id;
                            $res=M("u_record")->where("user_id='".$user."'")->save($data);
                            if($res>0){
                                print_r(json_encode(array("res"=>$res,"productn"=>$productn)));
                            }else{
                                print_r(json_encode(array("res"=>"1","productn"=>"")));
                            }
                            
                        }else{
                            print_r(json_encode(array("res"=>"-2","productn"=>"")));
                        }
                    }else{
                        print_r(json_encode(array("res"=>"-1","productn"=>"")));
                    }
                } catch (\Exception $e) {
                    print_r(json_encode(array("res"=>"0","productn"=>"")));
                }
            }            
        //领奖页面开始
            public function get_prize(){
                        /*
             * 
             * 只有我自己的翻牌的页面中才会有分享页面的按钮。这个时候连接是没有任何ID的。所以只能通过微信分享接口去做分享的功能。
             * 通过微信自带的分享都是分享的翻牌初始连接。
             * 
             * 如果他是通过微信自带的分享怎么办？
             * 我要一开始就要加上openid;
             * 如果是通过我写的脚本分享。那就我自己组装分享的连接。
             * 我的ID 无论何时都要有，因为他任何操作都要用到。好友的ID只有在帮他翻牌的时候才用到。
             * 如果，好友的ID存在了，我的没存在，那么在获取我的ID的时候好友的ID就没了。但是如果用session存储
             * 好友的ID的话那么这个好友的ID就一直存在了。
             * 所以如果是为好友翻牌的话那么自己的ID就不用要。如果在好友的翻牌页面的话。想要自己翻拍那么单独给一个进入翻牌的初始连接。
             * */
                $redirect_uri="http://www.51wxwx.com/weiObject/dxhd/index.php/Home/Index/get_prize";
                if(isset($_GET["code"])){
                    $openid=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appid&secret=$this->appSecret&code=".$_GET["code"]."&grant_type=authorization_code");
                    $openid=json_decode($openid);
                    if(isset($openid->errcode)){
                        echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                        exit;
                    }else{
                        $userinfo=file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=".$openid->access_token."&openid=".$openid->openid);
                        if($userinfo){
                            $userinfo=json_decode($userinfo);
                            $this->assign("headimg",$userinfo->headimgurl);
                        }
                        /**
                         * 在帮自己翻牌里面才会有分享的按钮。
                         * 1.微信appid 
                         * 2.时间戳 
                         * 3.随机字符串  
                         * 4.签名
                         * 拿到签名的步骤：
                         * 4.1 拿到access_token
                         * 4.2 拿到jsapi_ticket（有效期7200秒，开发者必须在自己的服务全局缓存jsapi_ticket）
                         * 4.3 生成签名
                         */
                        $jssdk = new JSSDK("$this->appid", "$this->appSecret");
                        $signPackage = $jssdk->GetSignPackage();
                        $this->assign("signPackage",$signPackage);
                        $this->assign("userid",$openid->openid);
                        //获取所有的卡片
                        $cardList=M("card")->select();
                        //获取已拥有的卡片和这张卡片的数量
                        $sql1="SELECT card_id,SUM(coun) AS coun FROM (SELECT card_id,COUNT(1) AS coun FROM uf_record WHERE 
                            fuser_id='".$openid->openid."' GROUP BY card_id) AS atab GROUP BY atab.card_id";
                        $cardnum=M("")->query($sql1);
                        foreach ($cardList as $key => $value) {
                            foreach ($cardnum as $key1 => $value1) {
                               if($value1["card_id"]==$value["card_id"]){
                                   $value["num"]=$value1["coun"];
                               }
                            }
                            $cardList[$key]=$value;
                        }
                        $this->assign("cardList",$cardList); //把所有的卡片和我拥有的卡片数据组合下
                        //获取已领奖的客户字段（u_record）/手机号码/手机号码不为空的count()/u_record和uf_record数据的总和/
                       
                        //$urecord所有手机号码
                        $urecord=M("u_record")->where("ipone!=''")->select();
                        foreach ($urecord as $key => $value) {
                            $pattern = "/(\d{3})\d{4}(\d{4})/";
                            $replacement = "\$1****\$2";
                            $ipone1= preg_replace($pattern, $replacement, $value["ipone"]);
                            $value["ipone"]=$ipone1;
                            $urecord[$key]=$value;
                        }
                        $this->assign("urecord",$urecord);
                        //带翻牌x
                        $this->assign("ufEcord",count(M("uf_record")->where("fuser_id='".$openid->openid."'")->select()));
                        //已翻牌
                        //判断是否已经点击领奖但是还未领奖的状态：is_true0:未领取1：已领取
                        $uprize=M("u_record")->join("left join prize on u_record.prize_id=prize.prize_id")->where("u_record.is_true=0 and u_record.prize_id IS NOT NULL and u_record.user_id='".$openid->openid."'")->find();
                        if($uprize){
                            //如果查到了那就直接显示领奖页面
                            $this->assign("uprize",$uprize);      
                        }else{
                            //如果没查到就再次查询是否已经领过奖品
                            $u_record=M("u_record")->join("left join prize on u_record.prize_id=prize.prize_id")->where("user_id='".$openid->openid."'")->find();
                            $this->assign("uEcord",$u_record?1:0);
                            $this->assign("u_record",$u_record);                            
                        }
                        //u_recordC人已经兑换奖品
                        $u_recordC=count($urecord);
                        $this->assign("u_recordC",$u_recordC);
                        //总共x人参与
                        $dfsql="SELECT * FROM(SELECT * FROM uf_record WHERE fuser_id=user_id) AS gu GROUP BY gu.user_id";
                        $ufcount=count(M("uf_record")->query($dfsql));
                        $ucount=count(M("u_record")->select());
                        $this->assign("tcount",$ufcount+$ucount);
                        //输出是否已经领取过奖励
                        $sql="SELECT * FROM (SELECT card_id FROM uf_record where fuser_id='".$openid->openid."' GROUP BY card_id) AS atab GROUP BY card_id";
                        $fuc=M("u_record")->query($sql);
                        $this->assign("fuc",count($fuc));                        
                        
                        $this->display("prize");
                    }
                }else{
                    echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                    exit;
                }        
            }        

        //领奖页面结束
        /**
         * 函数用途描述:
         *2018年4月21日 下午11:43:27
         * 作者：魏博宇
         */
        public function gouser_coutn(){
            try {
                //获得加密$openid
                $openidstr=file_get_contents("http://www.51wxwx.com/weiObject/dxhd/index.php/Home/Index/itv_encode?openid=oOcisjoq15yIJDUzgJU5JRQ8UfUk");
                $resJson=json_decode($openidstr);
                $resstr=file_get_contents("http://www.51wxwx.com/weiObject/dxhd/index.php/Home/Index/user_coutn_add?openid=".$resJson->code);
                print_r($resstr);
                $resJson=json_decode($resstr);
                print_r($resJson);
            } catch (\Exception $e) {
                echo "异常了";
            }
        }
            /**
             * 逻辑错了。我因该把加密之后的openid给他带过去。
             * 我直接把openid加密好放到页面。防止有人用url漏洞刷票
             * 
             * 现在就差一个新增次数的页面
             * 一个加密的接口
             * 1.数据库加一个次数字段
             * 2.添加翻牌机会的时候通过好友翻牌接口给自己翻牌
             * 3.翻牌方法需要修改
             * 函数用途描述:添加翻牌的次数接口
             *2018年4月21日 下午10:29:12
             * 作者：魏博宇
             */
            
        public function user_coutn_add($openid){
                try {
                    if($openid){
                        $openid=$this->decode($openid);
                        //次数加+1开始
                        $urecord=M("u_record")->where("user_id='$openid'")->find();
                        $data["use_count"]=++$urecord["use_count"];
                        $data["is_order"]=1;
                        $res=M("u_record")->where("user_id='$openid'")->save($data);
                        if($res>0){
                            echo "1";
                        }else{
                            //如果新增失败就记录日志
                            error_log("\r\n新增次数".date("y-m-d h:i:s").":".M("u_record")->getLastSql(),3,"./errors.log");
                        }
                        //次数加+1结束                        
                    }else{
                        echo "-1";
                        error_log("\r\n新增次数".date("y-m-d h:i:s").":未获得openid",3,"./errors.log");
                    }
                } catch (\Exception $e) {
                    echo "-1";
                    error_log("\r\n新增次数".date("y-m-d h:i:s").":".$e,3,"./errors.log");
                }
            }
            /**
             * 函数用途描述:专给iTV加密ID的接口
             *2018年4月21日 下午11:42:52
             * 作者：魏博宇
             */            
            public function itv_encode(){
                try {
                    $openid=I("openid");
                    if($openid){
                        echo json_encode(array("msg"=>"成功","code"=>$this->encode($openid)));
                    }else{
                        echo json_encode(array("msg"=>"未获得openid","code"=>"-1"));
                    }
                } catch (\Exception $e) {
                    echo json_encode(array("msg"=>"加密出现异常请联系接口开发人员","code"=>"-2"));
                }
            }
            /**
             * 函数用途描述:订购产品接口get接口
             *2018年4月22日 下午2:47:38
             * 作者：魏博宇
             */
            public function go_order(){
                try {
                    $ipone=I("ipone");
                    $openid=I("openid");
                    $productType=I("productType");
                    /**
                     *需要获取的：
                     *1：手机号码
                     *2： 产品选择类型1月的还是1年的
                    */
                    if($ipone&&$openid&&$productType){
                        //localhost/dxhd/index.php/Home/Index/go_order
                        $curl="http://mobile.ffcs.com.ahct.lv1.vcache.cn:10001/itv-api/order";
                        $providerId="ahdx"; //由电信统一分配，特权码订购时为ahdx
                        //www.51wxwx.com/weiObject/dxhd
                        $returnUrl="http://www.51wxwx.com/weiObject/dxhd"; //页面跳转同步通知地址
                        $notifyUrl="http://www.51wxwx.com/weiObject/dxhd/index.php/Home/Index/notifyUrl?openid=$openid"; //后台接口异步通知地址
                        $orderId=time().rand(0, 1000);     //订单号:见附录5.1.3  需要对方提供：唯一就行
                        $itvAccount="$ipone@123";  //ITV账号:见附录5.1.2  需要对方提供：用户手机号+@+渠道编码(渠道编码先随便写)
                        $productId="tvcode";   //产品编码:见附录5.1.4  需要对方提供:厂家字母缩写(ahdx)+内容缩写()+SD
                        //产品开始
                        //contentId=54|contentName=特权码-影视会员包月(月)
                        if($productType==1){
                            $contentId="54";   //内容编码:9元1月ID和155元1年的ID需要对方提供  5.1.5  目前只有1月的1年的没给我
                            $contentName="特权码-影视会员包月";
                            $price="1";
                        }else{
                            $contentId="54";   //内容编码:9元1月ID和155元1年的ID需要对方提供  5.1.5  目前只有1月的1年的没给我
                            $contentName="特权码-影视会员包年";
                            $price="100";
                        }
                        //产品结束
                        //加密开始
                        $orderInfo="";  //包含ITV账号、产品编码等加密过数据，数据用|线分隔，3DES加密
                        $key = '1464b900346646099b265c7bca650d59';
                        $iv = '01234567';
                        $msg = "itvAccount=$itvAccount|productId=$productId|orderId=$orderId|contentId=$contentId|contentName=$contentName|price=$price";
                        $orderInfo =Encrypt::encrypt($msg,$key,$iv);
                        $orderInfo=URLEncode($orderInfo);
                        //加密结束
                        $parent="notifyUrl=$notifyUrl&returnUrl=$returnUrl&providerId=$providerId&orderInfo=$orderInfo";
                        echo $curl."?".$parent."&deviceType=wap";
                    }else{
                        echo "-2"; //手机号没获取到
                    }
                } catch (\Exception $e) {
                    echo "-1";  //异常
                }
            }            
            /**
             * 函数用途描述:页面跳转同步通知地址
             *2018年4月22日 下午3:51:22
             * 作者：魏博宇
             */
            public function returnUrl(){
                try {
                    /**
                     * 1：支付成功会携带tradeInfo参数 
                     * 2：失败时，会携带errorInfo参数
                     */
                    echo success;
                } catch (\Exception $e) {
                    echo success;
                }
            }
            /**
             * 函数用途描述:后台接口异步通知地址
             *2018年4月22日 下午3:51:22
             * 作者：魏博宇
             */
            public function notifyUrl(){
                try {
                    $openid=$_GET["openid"];
                    $str=file_get_contents("php://input");
                    $kv=explode("&",$str);
                    foreach ($kv as $key => $value) {
                        $kv2=explode("=",$value);
                        if($kv2[0]=="providerId"&&$openid){
                            $this->user_coutn_add($openid);
                        }
                    }
                    $str="完整的url".'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
                    error_log("\r\n后台接口异步通知地址".date("y-m-d h:i:s").":".$str,3,"./errors.log");
                    
                    echo success;
                } catch (\Exception $e) {
                    error_log("\r\n后台接口异步通知地址".date("y-m-d h:i:s").":"."错误了$e",3,"./errors.log");
                    echo success;
                }
            }        
            /**
             * 函数用途描述:订购产品接口get接口
             *2018年4月22日 下午2:47:38
             * 作者：魏博宇
             */
            public function get_orderInfo(){
                try {
                    $ipone=I("ipone");
                    if($ipone){
                        $productId="ahdx";
                        $price="900";
                        $itvAccount="$ipone@123";
                        $contentId="54";
                        $contentName="特权码-影视会员包月";
                        $orderId=time().rand(0, 1000);
                        //加密开始
                        $orderInfo="";  //包含ITV账号、产品编码等加密过数据，数据用|线分隔，3DES加密
                        $key = '7749b74b9dee40b689f24c33c437ec1f';
                        $iv = '01234567';
                        $msg = "orderId=$orderId|productId=$productId|price=$price|itvAccount=$itvAccount|contentId=$contentId|contentName=$contentName";
                        $orderInfo =Encrypt::encrypt($msg,$key,$iv);
                        echo $orderInfo;
                        //加密结束
                    }else{
                        echo "-2";  //手机号码未获取到
                    }
                } catch (\Exception $e) {
                    echo "-1";  //异常
                }
            }
            /**
             * 函数用途描述:订购产品接口
             *2018年4月22日 下午2:47:38
             * 作者：魏博宇
             */
            function order_product(){
                header("Content-type: text/html; charset=UTF-8");
                //短信发送接口地址
                $ipone="18256089756";
                    $curl11="http://61.191.45.116:7002/itv-api/order";
                    $providerId="ahdx"; //由电信统一分配，特权码订购时为ahdx
                    $returnUrl="http://www.51wxwx.com/weiObject/dxhd/index.php/Home/Index/returnUrl"; //页面跳转同步通知地址
                    $notifyUrl="http://www.51wxwx.com/weiObject/dxhd/index.php/Home/Index/notifyUrl"; //后台接口异步通知地址
                    $orderId=time().rand(0, 1000);     //订单号:见附录5.1.3  需要对方提供：唯一就行
                    $itvAccount="$ipone@123";  //ITV账号:见附录5.1.2  需要对方提供：用户手机号+@+渠道编码(渠道编码先随便写)
                    $productId="tvcode";   //产品编码:见附录5.1.4  需要对方提供:厂家字母缩写(ahdx)+内容缩写()+SD
                    //产品开始
                    //contentId=54|contentName=特权码-影视会员包月(月)
                    $contentId="54";   //内容编码:9元1月ID和155元1年的ID需要对方提供  5.1.5  目前只有1月的1年的没给我
                    $contentName="特权码-影视会员包月";
                    $price="900";
                    //产品结束
                    //加密开始
                    $orderInfo="";  //包含ITV账号、产品编码等加密过数据，数据用|线分隔，3DES加密
                    $key = '7749b74b9dee40b689f24c33c437ec1f';
                    $iv = '01234567';
                    $msg = "orderId=$orderId|productId=$productId|price=$price|itvAccount=$itvAccount|contentId=$contentId|contentName=$contentName";
                    $orderInfo =Encrypt::encrypt($msg,$key,$iv);
                    //加密结束
               /*      $parent="providerId=$providerId&orderInfo=$orderInfo&returnUrl=$returnUrl&notifyUrl=$notifyUrl&
                    orderId=$orderId&itvAccount=$itvAccount&productId=$productId&contentId=$contentId
                    &contentName=$contentName&price=$price";
                    echo $curl."?".$parent;  */                   
                $data='{"providerId":"'.$providerId.'",
                       "orderInfo":"'.$orderInfo.'",
                       "returnUrl":"'.$returnUrl.'",
                       "notifyUrl":"'.$notifyUrl.'"
                        }';
            
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $curl11);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($curl);
                print_r($result);exit;
                if (curl_errno($curl)) {
                    return 0;
                }
                curl_close($curl);
                return $result;
            }
            /**
             * 测试获取是否关注公众号方法
             */
        public function gzgzh(){
            $redirect_uri="http://www.51wxwx.com/weiObject/dxhd/index.php/Home/Index/gzgzh";
            if(isset($_GET["code"])){
                $openid=file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appid&secret=$this->appSecret&code=".$_GET["code"]."&grant_type=authorization_code");
                $openid=json_decode($openid);
                if(isset($openid->errcode)){
                    echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                    exit;
                }else{
                    /* invalid credential, access_token is invalid or not latest hint: [AXRVdA0544vr69!]
                    https://api.weixin.qq.com/cgi-bin/user/info */
                    $access_token=$this->getAccessToken($this->appid,$this->appSecret);
                    $userinfo=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=".$openid->openid);
                        $userinfo=json_decode($userinfo);
                        print_r($userinfo);
                    /**
                     * 在帮自己翻牌里面才会有分享的按钮。
                     * 1.微信appid 
                     * 2.时间戳 
                     * 3.随机字符串  
                     * 4.签名
                     * 拿到签名的步骤：
                     * 4.1 拿到access_token
                     * 4.2 拿到jsapi_ticket（有效期7200秒，开发者必须在自己的服务全局缓存jsapi_ticket）
                     * 4.3 生成签名
                     */
                }
            }else{
                echo "<script>location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect'</script>";
                exit;
            }      
         }        
           /**
            * 获取access_token
            * @param string $appid
            * @param unknown $secret
            */
            public function getAccessToken($appid="",$secret) {
                // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
                $data = json_decode(file_get_contents("./access_token.json"));
                if($data){
                    if ($data->expire_time < time()) {
                        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
                        $res = json_decode(file_get_contents($url));
                        $access_token = $res->access_token;
                        if ($access_token) {
                            $data->expire_time = time() + 7000;
                            $data->access_token = $access_token;
                            $fp = fopen("./access_token.json", "w");
                            fwrite($fp, json_encode($data));
                            fclose($fp);
                        }
                    } else {
                        $access_token = $data->access_token;
                    }
                }
                return $access_token;
            }
            /**
             * 函数用途描述:验证号码是否已经领取过奖品
             *2018年4月23日 上午12:24:57
             * 作者：魏博宇
             */
            public function yz_iphone(){
                try {
                    $ipone=I("ipone");
                    $res=M("u_record")->where("ipone='$ipone'")->select();
                    if($res){
                        echo 1; //说明已经有人用过了
                    }else{
                        echo 0;
                    }
                } catch (\Exception $e) {
                    echo 1;
                }
            }
                /**
                 * 函数用途描述:验证手机号码是否为安徽电信
                 * 下午4:23:43
                 * 作者：魏博宇
                 */
                public function yzjiphone(){ 
                    //18055197645
                    try {
                        $iphone=I("ipone");
                        $stri=substr($iphone,0,7);
                        $yzs=M("yzjiphone")->where("iphone=$stri")->find();
                        if($yzs){
                            echo 1;
                        }else{
                            echo 0;
                        }
                    } catch (\Exception $e) {
                        echo 0;
                    }
                }
            /* TRUNCATE TABLE uf_record;
             TRUNCATE TABLE u_record; */
            /*  UPDATE dxhd.card SET probability_s = '1' , probability_m = '2'  WHERE card_id = 3;
             UPDATE dxhd.card SET probability_s = '3' , probability_m = '10'  WHERE card_id = 2;
             UPDATE dxhd.card SET probability_s = '11' , probability_m = '100'  WHERE card_id = 5;
             UPDATE dxhd.card SET probability_s = '101' , probability_m = '1000'  WHERE card_id = 8;
             UPDATE dxhd.card SET probability_s = '1001' ,  probability_m = '2000'  WHERE card_id = 7;
             UPDATE dxhd.card SET probability_s = '1000000' ,  probability_m = '1199999'  WHERE card_id = 6;
             UPDATE dxhd.card SET probability_s = '1200000' ,  probability_m = '1599999'  WHERE card_id = 1;
             UPDATE dxhd.card SET probability_s = '1600000' ,  probability_m = '2000000'  WHERE card_id = 4;
             */
            //卡牌
/*             INSERT  INTO `card`(`card_id`,`title`,`day_count`,`a_count`,`s_count`,`count`,`is_open`,`probability_s`,`probability_m`,`img_url`) VALUES (1,'博格巴',1,0,1,1,0,'1','2','3.png');
            INSERT  INTO `card`(`card_id`,`title`,`day_count`,`a_count`,`s_count`,`count`,`is_open`,`probability_s`,`probability_m`,`img_url`) VALUES (2,'阿圭罗',1,0,8,8,0,'3','10','2.png');
            INSERT  INTO `card`(`card_id`,`title`,`day_count`,`a_count`,`s_count`,`count`,`is_open`,`probability_s`,`probability_m`,`img_url`) VALUES (3,'厄齐尔',80,0,1000,1000,0,'101','1000','5.png');
            INSERT  INTO `card`(`card_id`,`title`,`day_count`,`a_count`,`s_count`,`count`,`is_open`,`probability_s`,`probability_m`,`img_url`) VALUES (4,'莱万',2147483626,21,2147483626,2147483647,1,'1000000','1199999','6.png');
            INSERT  INTO `card`(`card_id`,`title`,`day_count`,`a_count`,`s_count`,`count`,`is_open`,`probability_s`,`probability_m`,`img_url`) VALUES (5,'伊布',9,0,125,125,0,'11','100','8.png');
            INSERT  INTO `card`(`card_id`,`title`,`day_count`,`a_count`,`s_count`,`count`,`is_open`,`probability_s`,`probability_m`,`img_url`) VALUES (6,'布冯',2147483616,31,2147483616,2147483647,1,'1600000','2000000','4.png');
            INSERT  INTO `card`(`card_id`,`title`,`day_count`,`a_count`,`s_count`,`count`,`is_open`,`probability_s`,`probability_m`,`img_url`) VALUES (7,'梅西',1850,0,27020,27020,0,'1001','2000','7.png');
            INSERT  INTO `card`(`card_id`,`title`,`day_count`,`a_count`,`s_count`,`count`,`is_open`,`probability_s`,`probability_m`,`img_url`) VALUES (8,'C罗',2147483606,41,2147483606,2147483647,1,'1200000','1599999','1.png'); */
            //http://www.51wxwx.com/weiObject/dxhd/index.php/Home/Index/go_F?fuserid=oOcisjoq15yIJDUzgJU5JRQ8UfUk
}