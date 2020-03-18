<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
    public function _initialize() {
        try {
            if(!$_SESSION["username"]){
                $this->wby_alerturlNew("请先登录！","/weiObject/dxhd/admin.php/Home/Index/index");
            }
        } catch (\Exception $e) {
            $this->wby_alerturlNew("出现异常！","/weiObject/dxhd/admin.php/Home/Index/index");
        }
    }
        public function index(){
            $this->assign("username",$_SESSION["username"]);
            $this->assign("uname",$_SESSION["uname"]);
            $this->display("");
        }
        /**
         * 函数用途描述:显示所有领取 
         * 下午2:22:44
         * 作者：魏博宇
         */
        public function u_record_List(){
            try {
                $title=I("title","");
                $sql="SELECT * FROM(SELECT u_record.*,prize.title FROM u_record LEFT JOIN prize ON u_record.prize_id=prize.prize_id WHERE u_record.is_true=1) AS a WHERE  a.user_name IS NULL or a.user_name LIKE '%$title%'";
                print_r(json_encode(M("u_record")->query($sql)));
            } catch (\Exception $e) {
                echo "";
            }
        }
            /**
             * 函数用途描述:奖品信息集合
             * 下午3:51:49
             * 作者：魏博宇
             */
            public function prizeListc(){
                try {     
                    print_r(json_encode(M("prize")->select()));
                } catch (\Exception $e) {
                    echo "";
                }
            }
            /**
             * 函数用途描述:退出登录
             * 下午2:29:09
             * 作者：魏博宇
             */
            public function logout(){
                    session_destroy();
                    $this->wby_url("/weiObject/dxhd/admin.php");
            }
            /**
             * 函数用途描述:密码修改
             *2018年3月31日 下午3:11:20
             * 作者：魏博宇
             */
            public function save_ps(){
                try {
                    $data["mypassword"]=$this->encode(I("password"));
                    $res=M("myadmin")->where("myname='".$_SESSION["username"]."'")->save($data);
                    if($res>0){
                        $this->wby_alert("修改成功");
                    }else{
                        $this->wby_alert("修改失败");
                    }
                } catch (\Exception $e) {
                    $this->wby_alert("出现异常!");
                }
            }
}