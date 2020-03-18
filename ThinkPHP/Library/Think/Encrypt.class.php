<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;
/** 
* 3DES加解密类 
* @Author: 黎志斌 
* @version: v1.0 
* 2016年7月21日 
*/  
class Encrypt  
{  
  /*   private $key = "";  
    private $iv = "";  
    function __construct($key, $iv)  
    {  
        if (empty($key) || empty($iv)) {  
            echo 'key and iv is not valid';  
            exit();  
        }  
        $this->key = $key;  
        $this->iv = $iv;  
    }  
  
    public function encrypt7($value)  
    {  
        
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');  
        $iv = base64_decode($this->iv);  
        $value = $this->PaddingPKCS7($value);  
        $key = base64_decode($this->key);  
        mcrypt_generic_init($td, $key, $iv);  
        $ret = base64_encode(mcrypt_generic($td, $value));  
        mcrypt_generic_deinit($td);  
        mcrypt_module_close($td);  
        return $ret;  
    }  
    public function decrypt7($value)  
    {  
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');  
        $iv = base64_decode($this->iv);  
        $key = base64_decode($this->key);  
        mcrypt_generic_init($td, $key, $iv);  
        $ret = trim(mdecrypt_generic($td, base64_decode($value)));  
        $ret = $this->UnPaddingPKCS7($ret);  
        mcrypt_generic_deinit($td);  
        mcrypt_module_close($td);  
        return $ret;  
    }  
    private function PaddingPKCS7($data)  
    {  
        $block_size = mcrypt_get_block_size('tripledes', 'cbc');  
        $padding_char = $block_size - (strlen($data) % $block_size);  
        $data .= str_repeat(chr($padding_char), $padding_char);  
        return $data;  
    }  

    private function UnPaddingPKCS7($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    } 
    */
    /**
     * 加密5
     * @param unknown $value
     * @return Ambigous <boolean, string>
     */
    public function encrypt($input, $key, $iv, $base64 = true)
    {
        $size = 8;
        $input = self::pkcs5_pad($input, $size);
        $encryption_descriptor = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
        mcrypt_generic_init($encryption_descriptor, $key, $iv);
        $data = mcrypt_generic($encryption_descriptor, $input);
        mcrypt_generic_deinit($encryption_descriptor);
        mcrypt_module_close($encryption_descriptor);
        return base64_encode($data);
    }
    /**
     * 解密5
     * @param unknown $value
     * @return Ambigous <boolean, string>
     */
    public function decrypt($crypt, $key, $iv, $base64 = true)
    {
        $crypt = base64_decode($crypt);
        $encryption_descriptor = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
        mcrypt_generic_init($encryption_descriptor, $key, $iv);
        $decrypted_data = mdecrypt_generic($encryption_descriptor, $crypt);
        mcrypt_generic_deinit($encryption_descriptor);
        mcrypt_module_close($encryption_descriptor);
        $decrypted_data = self::pkcs5_unpad($decrypted_data);
        return rtrim($decrypted_data);
    }
    private function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    
    private function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)){
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
    

}
?>  