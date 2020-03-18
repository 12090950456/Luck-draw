<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        if(!$_SESSION["username"]){
            $this->wby_alerturln("denglu");
        }else{
            $this->wby_url("/weiObject/dxhd/admin.php/Home/User/index");
        }
    }
    public function yanzheng(){
        $username=I("username","");
        $password=I("password","");
        $myadmin=M("myadmin")->where("myname='$username' and mypassword='".$this->encode($password)."'")->find();
        if($myadmin){
            $_SESSION["username"]=$myadmin["myname"];
            $_SESSION["uname"]=$myadmin["uname"];
            $this->wby_alerturl("登录成功","/weiObject/dxhd/admin.php/Home/User/index");
        }else{
            $this->wby_alerturln("denglu","账号密码错误！");
        }
    }
}
