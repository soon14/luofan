
<?php
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

//刷新明细




function shuaxin_Log($loan_id,$loan_money,$log_info,$supplier_id,$supplier_name,$type=0)
{
    $log_ip = GetIp();
    $log_url = get_url();
    $data['log_info'] = $log_info;
    $data['supplier_id'] = $supplier_id;
    $data['supplier_name'] =$supplier_name;
    $data['loan_id'] = $loan_id;
    $data['loan_money'] = $loan_money;
    $data['log_ip'] = $log_ip;
    $data['log_url'] = $log_url;
    $data['log_time'] = time();
    $data['type'] = $type;
    $res = M("shuaxin_log")->add($data);
    if ($res) {
        return 1;
    } else {
        return 0;
    }
}

 /*
  *根据ip  获取省市区
 */
 function GetIpLookup($ip = ''){
	if(empty($ip)){
		$ip = GetIp();
	}
	$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
	if(empty($res)){ return false; }
	$jsonMatches = array();
	preg_match('#\{.+?\}#', $res, $jsonMatches);
	if(!isset($jsonMatches[0])){ return false; }
	$json = json_decode($jsonMatches[0], true);
	if(isset($json['ret']) && $json['ret'] == 1){
		$json['ip'] = $ip;
		unset($json['ret']);
	}else{
		return false;
	}
	return $json;
}

/*
  *获取IP
 */
function GetIp(){
		$realip = '';
		$unknown = 'unknown';
		if (isset($_SERVER)){
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				foreach($arr as $ip){
					$ip = trim($ip);
					if ($ip != 'unknown'){
						$realip = $ip;
						break;
					}
				}
			}else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){
				$realip = $_SERVER['HTTP_CLIENT_IP'];
			}else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){
				$realip = $_SERVER['REMOTE_ADDR'];
			}else{
				$realip = $unknown;
			}
		}else{
			if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){
				$realip = getenv("HTTP_X_FORWARDED_FOR");
			}else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){
				$realip = getenv("HTTP_CLIENT_IP");
			}else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){
				$realip = getenv("REMOTE_ADDR");
			}else{
				$realip = $unknown;
			}
		}
		$realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
		return $realip;
}

/**
*   获取随机邀请码
*/
function generate_code( $length = 6 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = '';
    for ( $i = 0; $i < $length; $i++ )
    {
    // 这里提供两种字符获取方式
    // 第一种是使用 substr 截取$chars中的任意一位字符；
    // 第二种是取字符数组 $chars 的任意元素
    // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $password;
}

  
function get_week($time){
	$arr = array("周一","周二","周三","周四","周五","周六","周日");
	$num =  date("N",$time);
	return $arr[$num-1];
}
  
function run_year($year){
	$time = mktime(20,20,20,2,1,$year);//取得一个日期的 Unix 时间戳;  
	if (date("t",$time)==29){ //格式化时间，并且判断2月是否是29天；  
	return $year."-02-29";//是29天就输出时闰年；  
	}else{  
	return $year."-02-28";  
	}
}


//给定一个年  返回该年的每个月的首尾时间戳  $year =2017 2016 
function get_month_for_year($year=""){
	if(!$year){
		$year=date(Y,time());
	}
	$now_year=date(Y,time());
	if($now_year==$year){
		$ii=date(n,time());
	}else{
		$ii=12;
	}
	for($i=1;$i<=$ii;$i++){
		$month[$i][1]= strtotime($year."-".$i."-01 00:00:00");  //2015-01-01 00:00:00
		if(in_array($i,array('1','3','5','7','8','10','12'))){
			$month[$i][2]= strtotime($year."-".$i."-31 23:59:59");  //2015-12-31 23:59:59
		}elseif(in_array($i,array('2'))){
			$res=run_year($year);
			$month[$i][2]= strtotime($res." 23:59:59");  //2015-12-31 23:59:59
		}elseif(in_array($i,array('4','6','9','11'))){
			$month[$i][2]= strtotime($year."-".$i."-30 23:59:59");  //2015-12-31 23:59:59
		}
	}
	return $month;
}


//给定一个月份  返回该月中每天的首尾时间戳 $month= 2017-03
function get_day_for_month($month=""){
	
	if(!$month){
		$month=date(Y,time())."-".date(m,time());
	}	
	//根据当前时间获取当前的年月
	$now_month=date(Y,time())."-".date(m,time());
	if($now_month == $month){
		$all_day=date(j,time());
	}else{
		$all_day = date(t,strtotime($month));
	}

	$day=array();
	for($i=1;$i<=$all_day;$i++){
		$day[$i][1]= strtotime($month."-".$i." 00:00:00");  //2015-01-01 00:00:00
		$day[$i][2]= strtotime($month."-".$i." 23:59:59");  //2015-12-31 23:59:59
	}
	return $day;
}


//给定一个日  返回当天的首尾时间戳  $day= 2017-03-11
function get_day_time($day=""){
	if(!$day){
		$day=date(Y,time())."-".date(m,time())."-".date(d,time());
	}

	$cache[1]=strtotime($day." 00:00:00");
	$cache[2]=strtotime($day." 23:59:59");
	return $cache;
	
}

//给定两天时间  返回两天的首尾时间戳  2017-03-01    2017-03-11
function get_twoday_time($start,$end){
	if($start=="" || $end==""){
		return false;
	}
	$cache[1]=strtotime($start." 00:00:00");
	$cache[2]=strtotime($end." 23:59:59");
	return $cache;
	
}


//获取某一月的 首尾时间戳    2017-01
function get_month_time($month=""){
	if(!$month){
		$month = date(Y,time())."-".date(m,time());
		$ii    = date(n,time());//月份
	}else{
		$ii  = substr($month,-2);
	}	
	
	$cache[1]= strtotime($month."-01 00:00:00");  //2015-01-01 00:00:00
	if(in_array($ii,array('1','3','5','7','8','10','12'))){
		$cache[2]= strtotime($month."-31 23:59:59");  //2015-12-31 23:59:59
	}elseif(in_array($ii,array('2'))){
		$res=run_year($year);
		$cache[2]= strtotime($res." 23:59:59");  //2015-12-31 23:59:59
	}elseif(in_array($ii,array('4','6','9','11'))){
		$cache[2]= strtotime($month."-30 23:59:59");  //2015-12-31 23:59:59
	}
	return $cache;	
}


//给定两个月份 返回两月首尾时间戳    2017-01  2017-03
function get_twomonth_time($start,$end){
	if($start=="" || $end==""){
		return false;
	}
	$ii  = substr($end,-2);
	$cache[1]= strtotime($start."-01 00:00:00");  //2015-01-01 00:00:00
	if(in_array($ii,array('1','3','5','7','8','10','12'))){
		$cache[2]= strtotime($end."-31 23:59:59");  //2015-12-31 23:59:59
	}elseif(in_array($ii,array('2'))){
		$res=run_year($year);
		$cache[2]= strtotime($end." 23:59:59");  //2015-12-31 23:59:59
	}elseif(in_array($ii,array('4','6','9','11'))){
		$cache[2]= strtotime($end."-30 23:59:59");  //2015-12-31 23:59:59
	}
	return $cache;	
}



//获取某一年的 首尾时间戳    2017
function get_year_time($year=""){
	if(!$year){
		$year=date(Y,time());
	}
	$cache[1]= strtotime($year."-01-01 00:00:00"); 
	$cache[2]= strtotime($year."-12-31 23:59:59"); 
	return $cache;	
}

//给定两个年份  返回两年首尾时间戳  2015  2017
function get_twoyear_time($start,$end){
	if($start=="" || $end==""){
		return false;
	}
	$cache[1]= strtotime($start."-01-01 00:00:00"); 
	$cache[2]= strtotime($end."-12-31 23:59:59"); 
	return $cache;	
}
  
 

/**wzz
 * 密码加密的规则
 */
function encrypt_pass($pass){
	return md5($pass.md5(C('PASS_KEY')));
}

/**
 * 获取物流信息
 */
function get_express_info($express,$LogisticCode){

    $ShipperCode = $express;
    $LogisticCode = $LogisticCode;

    $EBusinessID = '1265531';
    $AppKey = '2fac2b65-0f6e-4738-abcb-52ff176ca90a';
    $ReqURL = 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';

    $requestData= "{'OrderCode':'','ShipperCode':'".$ShipperCode."','LogisticCode':'".$LogisticCode."'}";
    $datas = array(
        'EBusinessID' => $EBusinessID,
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = express_encrypt($requestData, $AppKey);
    $result=express_send_post($ReqURL, $datas);
	$express = json_decode($result,true);
    $wuliu = $express['Traces'];
	$wuliu = list_sort_by($wuliu,'AcceptTime','desc');
    return $wuliu;
}

/**
*  post提交数据
* @param  string $url 请求Url
* @param  array $datas 提交的数据
* @return url响应返回的html
*/
function express_send_post($url, $datas){
    $temps = array();
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);
    }
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
    if($url_info['port']=='')
    {
        $url_info['port']=80;
    }
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader.= "Connection:close\r\n\r\n";
    $httpheader.= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets = "";
    $headerFlag = true;
    while (!feof($fd)) {
        if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
            break;
        }
    }
    while (!feof($fd)) {
        $gets.= fread($fd, 128);
    }
    fclose($fd);

    return $gets;

}

/**
 * 电商Sign签名生成
 * @param data 内容
 * @param appkey Appkey
 * @return DataSign签名
 */
function express_encrypt($data, $appkey) {
    return urlencode(base64_encode(md5($data.$appkey)));
}





function deal_sku_data($data){
	$new_arr = array();
	foreach($data as $v){
		$new_arr[$v['attr_list']] = array(
				"oprice" => $v['oprice'],
				"integral"  => $v['integral'],
				"store"  => $v['store'],
				//"id"     => $v['id'],
			);
	}
	return $new_arr;
}

/**
 * 组合各选项生成sku的方法
array(2) {
	  array(4) {
		    array(2) {
		      ["id"] => string(1) "2"
		      ["classname"] => string(6) "红色"
		    }
		    array(2) {
		      ["id"] => string(1) "4"
		      ["classname"] => string(6) "蓝色"
		    }
	  }
	  array(5) {
		    array(2) {
		      ["id"] => string(1) "8"
		      ["classname"] => string(1) "S"
		    }
		    array(2) {
		      ["id"] => string(1) "9"
		      ["classname"] => string(1) "M"
		    }
	  }
}

组合生成，类似以下的数组

	array(
		array(
			array("id"=>1,"classname"=>"红色"),
			array("id"=>8,"classname"=>"S"),
		),
		array(
			array("id"=>1,"classname"=>"红色"),
			array("id"=>9,"classname"=>"M"),
		),
		.....
	)
 */
function mixSku($arr){
	$firstarr = array_shift($arr);
	$newOne;
	foreach($firstarr as $k=>$v){
		$newOne[$k][] = $v;
	}
	foreach($arr as $v){
		$newOne = doMixSku($newOne, $v);
	}
	return $newOne;
}

function doMixSku($arr1,$arr2){
	$newarr;
	foreach($arr1 as $v){
		foreach($arr2 as $vv){
			$a = $v;
			$a[] = $vv;
			$newarr[] = $a;
		}
	}
	return $newarr;
}

/**
 * 生成table的方法
 * @param arr sku生成过后的数组
 */
function makeTable($arr, $other=""){
	$str = "";
	foreach($arr as $v){
		$str .= "<tr>";
		foreach($v as $vv){
			$str .= "<td class='sku-attr-data' data-id='{$vv[id]}'>{$vv[classname]}</td>";
		}
		$str .= $other;
		$str .= "</tr>";
	}
	return $str;
}

/**
 * 导出数据为excel表格
 *@param $data    一个二维数组,结构如同从数据库查出来的数组
 *@param $title   excel的第一行标题,一个数组,如果为空则没有标题
 *@param $filename 下载的文件名
 *@examlpe
 *$stu = M ('User');
 *$arr = $stu -> select();
 *exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
 */
function easyExcel($data = array(), $title = array(), $filename = 'report') {
	header("Content-type:application/octet-stream");
	header("Accept-Ranges:bytes");
	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=" . $filename . ".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	//导出xls 开始
	if (!empty($title)) {
		foreach ($title as $k => $v) {
			$title[$k] = iconv("UTF-8", "GB2312", $v);
		}
		$title = implode("\t", $title);
		echo "$title\n";
	}
	if (!empty($data)) {
		foreach ($data as $key => $val) {
			foreach ($val as $ck => $cv) {
				$data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
			}
			$data[$key] = implode("\t", $data[$key]);

		}
		echo implode("\n", $data);
	}

}



/**
 * 找不到商品时，回退
 */
function goback($msg="没有响应信息！"){
	echo "<script>alert($msg);window.history.back()</script>";die;
}


/**
 * dump,die
 */
function dd($data){
	dump($data);die();
}

/**
 * 生成订单号
 */
function getOrderSN($prefix="LDGY"){
	return $prefix.date("YmdHis", time()).mt_rand(100,999).$_SESSION["user_id"];
}


/**

 * 生成随机字符串，由小写英文和数字组成。去掉了容易混淆的0o1l之类

 * @param int $int 生成的随机字串长度

 * @param boolean $caps 大小写，默认返回小写组合。true为大写，false为小写

 * @return string 返回生成好的随机字串

 */

function randStr($int = 6, $caps = false) {

	$strings = 'abcdefghjkmnpqrstuvwxyz23456789';

	$return = '';

	for ($i = 0; $i < $int; $i++) {

		srand();

		$rnd = mt_rand(0, 30);

		$return = $return . $strings[$rnd];

	}

	return $caps ? strtoupper($return) : $return;

}

/**

 * 生成随机字符串，由小写英文和数字组成。去掉了容易混淆的0o1l之类

 * @param int $int 生成的随机字串长度

 * @param boolean $caps 大小写，默认返回小写组合。true为大写，false为小写

 * @return string 返回生成好的随机字串

 */

function randInt($int = 6, $caps = false) {

	$strings = '0123456789';

	$return = '';

	for ($i = 0; $i < $int; $i++) {

		srand();

		$rnd = mt_rand(0, 9);

		$return = $return . $strings[$rnd];

	}

	return $caps ? strtoupper($return) : $return;

}



 /**
  *时间差
 **/

function TwoDays ($day1, $day2)
{
	$second1 = strtotime($day1);
	$second2 = strtotime($day2);

	if ($second1 < $second2) {
		$tmp = $second2;
		$second2 = $second1;
		$second1 = $tmp;
	}
	return ($second1 - $second2) / 86400;
}

/**
 *招聘福利
 * @param int $rid 招聘信息id
**/

function get_label($rid){
	$M_recruitment_label=M("Recruitment_label");
	$M_label=M("Label");
	$arr=array();
	$i=0;
	$recruitment_labelinfo=$M_recruitment_label->where("recruitment_id=%d",array($rid))->select();
	foreach($recruitment_labelinfo as $v){
		$arr[$i]=$M_label->where(array('id'=>$v['label_id']))->getField("labelname");
		$i++;
	}

	return $arr;
}




 /**
 * 获得用户的真实IP地址
 *
 * @access  public
 * @return  string
 */
function real_ip() {
    static $realip = NULL;

    if ($realip !== NULL) {
        return $realip;
    }

    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip) {
                $ip = trim($ip);

                if ($ip != 'unknown') {
                    $realip = $ip;

                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}



/**
 * 检测用户是否登录
 *
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author chandler_qjw
 */
function is_login() {
	$user = session ( 'user_auth' );
	if (empty ( $user )) {
		return 0;
	}else {
		return session ( 'user_auth_sign' ) == data_auth_sign ( $user ) ? $user ['uid'] : 0;
	}
}
/**
 * 验证码检查
 */
function check_verify($code, $id = ""){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}



 /**wzz 20170425
  *发送验证码
  * $telephone		手机号
  * $code_type  1-注册  2-忘记密码   3-短信验证登录  4-免费申请贷款 5-小贷公司    15-贷款申请 
  1-注册   2-找回密码   3-验证码登录  4-首页免费申请 15-贷款申请  
 **/
function sendMessage($telephone,$code_type,$msg=""){
	header("Content-Type: text/html; charset=UTF-8");
	
	/******每日短信限制******/
	$time = date('Y-m-d',time());
	$cishu = M('Jilu')->where('num=1')->find();
	$ips = GetIP();
	$ipauth = M('Jilu')->where(array('ip'=>$ips,'time'=>$time,'code_type'=>$code_type))->count();
	$telauth = M('Jilu')->where(array('tel'=>$telephone,'time'=>$time,'code_type'=>$code_type))->count();
	if($ipauth > $cishu['ip_num']){
		return array("status"=>0,"info"=>"今日短信次数已上限，请明天再试！");
	}
	if($telauth > $cishu['tel_num']){
		return array("status"=>0,"info"=>"今日短信次数已上限，请明天再试！");
	}
	/******每日短信限制******/
	
	
	
	$flag    = 0; 
	$params  ='';
	$verify  = rand(123456, 999999);//获取随机验证码
	
	
	if($code_type==1){
		$content = "验证码：".$verify."您好，您正在注册牛轰轰官网会员，感谢您的支持！验证码有效期2分钟！";
	}elseif($code_type==2){
		$content = "验证码：".$verify."您好，您正在修改牛轰轰官网会员登录密码，请妥善保管个人信息！验证码有效期2分钟！";		
	}elseif($code_type==3){
		$content = "验证码：".$verify."您好，您正在登录牛轰轰官网，感谢您的支持！验证码有效期2分钟！";	
	}elseif($code_type==4){
		$content = "验证码：".$verify."您好，您正在牛轰轰官网申请免费贷款，感谢您的支持！验证码有效期2分钟！";	
	}elseif($code_type==5){
        $content = "验证码：".$verify."您好，您正在注册牛轰轰官网小贷公司，感谢您的支持！验证码有效期2分钟！";
    }elseif($code_type==6){
        $content = "验证码：".$verify."您好，您正在牛轰轰官网申请绑定银行卡服务，感谢您的支持！验证码有效期2分钟！";
    }elseif($code_type==7){
        $content = "验证码：".$verify."您好，您正在注册牛轰轰代理商，感谢您的支持！验证码有效期2分钟！";
    }elseif($code_type==8){
        $content = "验证码：".$verify."您好，您正在登录牛轰轰代理商，感谢您的支持！验证码有效期2分钟！";
    }elseif($code_type==15){
        $content = "验证码：".$verify."您好，您正在申请牛轰轰官网小贷公司贷款服务，感谢您的支持！验证码有效期2分钟！";
    }else{
		$content= $msg;
	}
	//以下信息自己填以下
	$argv = array( 
		/* 'name'     => 'dxwluofan',                       //必填参数。用户账号 */
		/* 'pwd'      => '9C2608D12F0EBD8A2F5745B9363C',   //必填参数。（web平台：基本资料中的接口密码） */
		'name'     => 'dxwniuhh',                       //必填参数。用户账号
		'pwd'      => '4BB03EF0233716C17D6B1E35CB8C',   //必填参数。（web平台：基本资料中的接口密码）
		'content'  => $content,                         //必填参数。发送内容（1-500 个汉字）UTF-8编码
		'mobile'   => $telephone,                       //必填参数。手机号码。多个以英文逗号隔开
		'stime'    => '',                               //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
		'sign'     => '【牛轰轰官网】',                 //必填参数。用户签名。
		'type'     => 'pt',                             //必填参数。固定值 pt
		'extno'    => ''                                //可选参数，扩展码，用户定义扩展码，只能为数字
	); 
	
	foreach ($argv as $key=>$value) { 
		if ($flag!=0) { 
			$params .= "&"; 
			$flag = 1; 
		} 
		$params.= $key."="; $params.= urlencode($value);// urlencode($value); 
		$flag = 1; 
	} 
	$url = "http://web.duanxinwang.cc/asmx/smsservice.aspx?".$params; //提交的url地址
	$con= substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态
	
	if($con == '0'){
		//保存数据库
		$db=M('user_verify');
		$data['telephone'] = $telephone;
		$data['code']      = $verify;
		$data['msg']       = $content;
		$data['add_time']  = time();
		$data['code_type'] = $code_type;  
		$data['status']    = 0;
		$db->add($data);
		
		$map['ip'] = GetIp();
		$map['tel'] = $telephone;
		$map['time'] = date('Y-m-d',time());
        $map['cord_type']   =   $code_type;
		M('Jilu')->add($map);
		
		
		return array("status"=>1,"info"=>"短信发送成功,请查收！");
		//echo "<script>alert('发送成功!');</script>";
	}else{
		return array("status"=>0,"info"=>"短信发送失败，请重试！");
		//echo "<script>alert('发送失败!');history.back();</script>";
	}
}
/**
 * 一个检测验证码的方法 （找密码验证）
 * $phone		手机号
 * $identify	验证码
 * status       状态0-未过期  1-完成注册验证 2-完成密码找回   3-完成登录 4-完成免费申请 5-小贷公司注册  9-已过期
 */
function checkMessage($phone, $identify, $status){
    $over_time=C('over_time');
    $now_time=time();
    $sql=M('user_verify')->where(array('telephone'=>$phone,"status"=>0))->order('add_time desc')->find();
    if(!$sql){
        return array("status"=>'2',"info"=>"验证码错误");
    }
    $addtime=$sql['add_time'];
    if ($now_time<($addtime+$over_time)){
        $code=$sql['code'];
        if ($code==$identify){
            M('user_verify')->where(array('id'=>$sql['id']))->save(array("status"=>$status));
            return array("status"=>'1',"info"=>"ok");
        }else {
            return array("status"=>'2',"info"=>"验证码错误");
        }
    }else{
        M('user_verify')->where(array('id'=>$sql['id']))->save(array("status"=>9));
        return array("status"=>'3',"info"=>"验证码已过期");
    }
}

/**
 * 一个检测邮箱验证码的方法 （找密码验证）
 * $phone		手机号
 * $identify	验证码
 * status       状态0-未过期  1-完成注册验证 2-完成密码找回   3-完成登录 4-完成免费申请 5-小贷公司注册  9-已过期
 */
function checkEmail($email, $identify, $status){
    $over_time=C('over_time');
    $now_time=time();
    $sql=M('email_code')->where(array('email'=>$email,"status"=>0))->order('addtime desc')->find();
    if(!$sql){
        return array("status"=>'2',"info"=>"验证码错误");
    }
    $addtime=$sql['addtime'];
    if ($now_time<($addtime+$over_time)){
        $code=$sql['code'];
        if (strtolower($code)==strtolower($identify)){
            M('email_code')->where(array('id'=>$sql['id']))->save(array("status"=>1));
            return array("status"=>'1',"info"=>"ok");
        }else {
            return array("status"=>'2',"info"=>"验证码错误");
        }
    }else{
        M('email_code')->where(array('id'=>$sql['id']))->save(array("status"=>99));
        return array("status"=>'3',"info"=>"验证码已过期");
    }
}


/**
 * 获取时间
 *
 * @return strtime  （2015-08-26 17:31:00）
 * @author chandler_qjw
 */
function Gettime() {
   return date('Y-m-d H:i:s',time());
}


/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10) {
    $p = new Think\Page($count, $pagesize);
    /* $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>'); */
    /* $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页'); */
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}


/**
 * 获取当前完整URl
 *
 * @return strtime  （2015-08-26 17:31:00）
 * @author chandler_qjw
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
 * 获取当前无参数URl
 *
 * @return strtime  （2015-08-26 17:31:00）
 * @author chandler_qjw
 */
function curPageURL()
{
    $pageURL = 'http';

    if ($_SERVER["HTTPS"] == "on")
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";

    $this_page = $_SERVER["REQUEST_URI"];

    // 只取 ? 前面的内容
    if (strpos($this_page, "?") !== false)
        $this_page = reset(explode("?", $this_page));

    if ($_SERVER["SERVER_PORT"] != "80")
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $this_page;
    }
    else
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $this_page;
    }
    return $pageURL;
}

/**
 * 获取当前域名
 *
 * @return strtime  （2015-08-26 17:31:00）
 * @author chandler_qjw
 */
function curHost()
{
    $pageURL = 'http';

    if ($_SERVER["HTTPS"] == "on")
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80")
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
    }
    else
    {
        $pageURL .= $_SERVER["SERVER_NAME"];
    }
    return $pageURL;
}



/*
*
* @param $data 二维码包含的文字内容
* @param $filename 保存二维码输出的文件名称，*.png
* @param bool $picPath 二维码输出的路径
* @param bool $logo 二维码中包含的LOGO图片路径
* @param string $size 二维码的大小
* @param string $level 二维码编码纠错级别：L、M、Q、H
* @param int $padding 二维码边框的间距
* @param bool $saveandprint 是否保存到文件并在浏览器直接输出，true:同时保存和输出，false:只保存文件
* return string
*/
 function qrcode($data,$filename,$picPath=false,$logo=false,$size='10',$level='L',$padding=2,$saveandprint=false){
     vendor("phpqrcode.phpqrcode");//引入工具包
     // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
     $path = $picPath?$picPath:SITE_PATH."\\Uploads\\Picture\\QRcode"; //图片输出路径
     mkdir($path);
     //在二维码上面添加LOGO
     if(empty($logo) || $logo=== false) { //不包含LOGO
         if ($filename==false) {
             QRcode::png($data, false, $level, $size, $padding, $saveandprint); //直接输出到浏览器，不含LOGO
         }else{
             $filename=$path.'/'.$filename; //合成路径
             QRcode::png($data, $filename, $level, $size, $padding, $saveandprint); //直接输出到浏览器，不含LOGO
         }
     }else { //包含LOGO
         if ($filename==false){
             //$filename=tempnam('','').'.png';//生成临时文件
             die('参数错误');
         }else {
             //生成二维码,保存到文件
             $filename = $path . '\\' . $filename; //合成路径
         }
         QRcode::png($data, $filename, $level, $size, $padding);
         $QR = imagecreatefromstring(file_get_contents($filename));
         $logo = imagecreatefromstring(file_get_contents($logo));
         $QR_width = imagesx($QR);
         $QR_height = imagesy($QR);
         $logo_width = imagesx($logo);
         $logo_height = imagesy($logo);
         $logo_qr_width = $QR_width / 5;
         $scale = $logo_width / $logo_qr_width;
         $logo_qr_height = $logo_height / $scale;
         $from_width = ($QR_width - $logo_qr_width) / 2;
         imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
         if ($filename === false) {
             Header("Content-type: image/png");
             imagepng($QR);
         } else {
             if ($saveandprint === true) {
                 imagepng($QR, $filename);
                 header("Content-type: image/png");//输出到浏览器
                 imagepng($QR);
             } else {
                 imagepng($QR, $filename);
             }
         }
     }
     return $filename;
 }

/**
 * 生成临时二维码
 */
function tempQrcode($data, $level='L', $size='10', $padding=2){
    vendor("phpqrcode.phpqrcode");//引入工具包
    QRcode::png($data, false, $level, $size, $padding);
}

/**
 * 检测当前用户是否为管理员
 *
 * @return boolean true-管理员，false-非管理员
 * @author chandler_qjw
 */
function is_administrator($uid = null) {
	$uid = is_null ( $uid ) ? is_login () : $uid;
	return $uid && (intval ( $uid ) === C ( 'USER_ADMINISTRATOR' ));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 *
 * @param string $str
 *        	要分割的字符串
 * @param string $glue
 *        	分割符
 * @return array
 * @author chandler_qje
 */
function str2arr($str, $glue = ',') {
	return explode ( $glue, $str );
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 *
 * @param array $arr
 *        	要连接的数组
 * @param string $glue
 *        	分割符
 * @return string
 * @author chandler_qjw
 */
function arr2str($arr, $glue = ',') {
	return implode ( $glue, $arr );
}

/**
 * 字符串截取，支持中文和其他编码
 *
 * @access public
 * @param string $str
 *        	需要转换的字符串
 * @param string $start
 *        	开始位置
 * @param string $length
 *        	截取长度
 * @param string $charset
 *        	编码格式
 * @param string $suffix
 *        	截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists ( "mb_substr" ))
		$slice = mb_substr ( $str, $start, $length, $charset );
	elseif (function_exists ( 'iconv_substr' )) {
		$slice = iconv_substr ( $str, $start, $length, $charset );
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all ( $re [$charset], $str, $match );
		$slice = join ( "", array_slice ( $match [0], $start, $length ) );
	}
	return $suffix ? $slice . '...' : $slice;
}
/**
 * 方法增强，根据$length自动判断是否应该显示...
 * 字符串截取，支持中文和其他编码
 * QQ:125682133
 *
 * @access public
 * @param string $str
 *        	需要转换的字符串
 * @param string $start
 *        	开始位置
 * @param string $length
 *        	截取长度
 * @param string $charset
 *        	编码格式
 * @param string $suffix
 *        	截断显示字符
 * @return string
 */
function msubstr_local($str, $start = 0, $length, $charset = "utf-8") {
	if (function_exists ( "mb_substr" ))
		$slice = mb_substr ( $str, $start, $length, $charset );
	elseif (function_exists ( 'iconv_substr' )) {
		$slice = iconv_substr ( $str, $start, $length, $charset );
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all ( $re [$charset], $str, $match );

		$slice = join ( "", array_slice ( $match [0], $start, $length ) );
	}
	return (strlen ( $str ) > strlen ( $slice )) ? $slice . '...' : $slice;
}
/**
 * 系统加密方法
 *
 * @param string $data
 *        	要加密的字符串
 * @param string $key
 *        	加密密钥
 * @param int $expire
 *        	过期时间 单位 秒
 * @return string
 * @author chandler_qjw
 */
function think_encrypt($data, $key = '', $expire = 0) {
	$key = md5 ( empty ( $key ) ? C ( 'DATA_AUTH_KEY' ) : $key );
	$data = base64_encode ( $data );
	$x = 0;
	$len = strlen ( $data );
	$l = strlen ( $key );
	$char = '';

	for($i = 0; $i < $len; $i ++) {
		if ($x == $l)
			$x = 0;
		$char .= substr ( $key, $x, 1 );
		$x ++;
	}

	$str = sprintf ( '%010d', $expire ? $expire + time () : 0 );

	for($i = 0; $i < $len; $i ++) {
		$str .= chr ( ord ( substr ( $data, $i, 1 ) ) + (ord ( substr ( $char, $i, 1 ) )) % 256 );
	}
	return str_replace ( array (
			'+',
			'/',
			'='
	), array (
			'-',
			'_',
			''
	), base64_encode ( $str ) );
}

/**
 * 系统解密方法
 *
 * @param string $data
 *        	要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key
 *        	加密密钥
 * @return string
 * @author chandler_qjw
 */
function think_decrypt($data, $key = '') {
	$key = md5 ( empty ( $key ) ? C ( 'DATA_AUTH_KEY' ) : $key );
	$data = str_replace ( array (
			'-',
			'_'
	), array (
			'+',
			'/'
	), $data );
	$mod4 = strlen ( $data ) % 4;
	if ($mod4) {
		$data .= substr ( '====', $mod4 );
	}
	$data = base64_decode ( $data );
	$expire = substr ( $data, 0, 10 );
	$data = substr ( $data, 10 );

	if ($expire > 0 && $expire < time ()) {
		return '';
	}
	$x = 0;
	$len = strlen ( $data );
	$l = strlen ( $key );
	$char = $str = '';

	for($i = 0; $i < $len; $i ++) {
		if ($x == $l)
			$x = 0;
		$char .= substr ( $key, $x, 1 );
		$x ++;
	}

	for($i = 0; $i < $len; $i ++) {
		if (ord ( substr ( $data, $i, 1 ) ) < ord ( substr ( $char, $i, 1 ) )) {
			$str .= chr ( (ord ( substr ( $data, $i, 1 ) ) + 256) - ord ( substr ( $char, $i, 1 ) ) );
		} else {
			$str .= chr ( ord ( substr ( $data, $i, 1 ) ) - ord ( substr ( $char, $i, 1 ) ) );
		}
	}
	return base64_decode ( $str );
}

/**
 * 数据签名认证
 *
 * @param array $data
 *        	被认证的数据
 * @return string 签名
 * @author chandler_qjw
 */
function data_auth_sign($data) {
	// 数据类型检测
	if (! is_array ( $data )) {
		$data = ( array ) $data;
	}
	ksort ( $data ); // 排序
	$code = http_build_query ( $data ); // url编码并生成query字符串
	$sign = sha1 ( $code ); // 生成签名
	return $sign;
}

/**
 * 对查询结果集进行排序
 *
 * @access public
 * @param array $list
 *        	查询结果
 * @param string $field
 *        	排序的字段名
 * @param array $sortby
 *        	排序类型
 *        	asc正向排序 desc逆向排序 nat自然排序
 * @return array
 *
 */
function list_sort_by($list, $field, $sortby = 'asc') {
	if (is_array ( $list )) {
		$refer = $resultSet = array ();
		foreach ( $list as $i => $data )
			$refer [$i] = &$data [$field];
		switch ($sortby) {
			case 'asc' : // 正向排序
				asort ( $refer );
				break;
			case 'desc' : // 逆向排序
				arsort ( $refer );
				break;
			case 'nat' : // 自然排序
				natcasesort ( $refer );
				break;
		}
		foreach ( $refer as $key => $val )
			$resultSet [] = &$list [$key];
		return $resultSet;
	}
	return false;
}

/**
 * 把返回的数据集转换成Tree
 *
 * @param array $list
 *        	要转换的数据集
 * @param string $pid
 *        	parent标记字段
 * @param string $level
 *        	level标记字段
 * @return array
 * @author chandler_qjw
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array ();
	if (is_array ( $list )) {
		// 创建基于主键的数组引用
		$refer = array ();
		foreach ( $list as $key => $data ) {
			$refer [$data [$pk]] = & $list [$key];
		}
		foreach ( $list as $key => $data ) {
			// 判断是否存在parent
			$parentId = $data [$pid];
			if ($root == $parentId) {
				$tree [] = & $list [$key];
			} else {
				if (isset ( $refer [$parentId] )) {
					$parent = & $refer [$parentId];
					$parent [$child] [] = & $list [$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 *
 * @param array $tree
 *        	原来的树
 * @param string $child
 *        	孩子节点的键
 * @param string $order
 *        	排序显示的键，一般是主键 升序排列
 * @param array $list
 *        	过渡用的中间数组，
 * @return array 返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
	if (is_array ( $tree )) {
		$refer = array ();
		foreach ( $tree as $key => $value ) {
			$reffer = $value;
			if (isset ( $reffer [$child] )) {
				unset ( $reffer [$child] );
				tree_to_list ( $value [$child], $child, $order, $list );
			}
			$list [] = $reffer;
		}
		$list = list_sort_by ( $list, $order, $sortby = 'asc' );
	}
	return $list;
}

/**
 * 格式化字节大小
 *
 * @param number $size
 *        	字节数
 * @param string $delimiter
 *        	数字和单位分隔符
 * @return string 格式化后的带单位的大小
 * @author chandler_qjw
 */
function format_bytes($size, $delimiter = '') {
	$units = array (
			'B',
			'KB',
			'MB',
			'GB',
			'TB',
			'PB'
	);
	for($i = 0; $size >= 1024 && $i < 5; $i ++)
		$size /= 1024;
	return round ( $size, 2 ) . $delimiter . $units [$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 *
 * @author chandler_qjw
 */
function set_redirect_url($url) {
	cookie ( 'redirect_url', $url );
}

/**
 * 获取跳转页面URL
 *
 * @return string 跳转页URL
 * @author chandler_qjw
 */
function get_redirect_url() {
	$url = cookie ( 'redirect_url' );
	return empty ( $url ) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 *
 * @param string $hook
 *        	钩子名称
 * @param mixed $params
 *        	传入参数
 * @return void
 */
function hook($hook, $params = array()) {
	\Think\Hook::listen ( $hook, $params );
}

/**
 * 获取插件类的类名
 *
 * @param strng $name
 *        	插件名
 */
function get_addon_class($name) {
	$class = "Addons\\{$name}\\{$name}Addon";
	return $class;
}

/**
 * 获取插件类的配置文件数组
 *
 * @param string $name
 *        	插件名
 */
function get_addon_config($name) {
	$class = get_addon_class ( $name );
	if (class_exists ( $class )) {
		$addon = new $class ();
		return $addon->getConfig ();
	} else {
		return array ();
	}
}

/**
 * 插件显示内容里生成访问插件的url
 *
 * @param string $url
 *        	url
 * @param array $param
 *        	参数
 * @author chandler_qjw
 */
function addons_url($url, $param = array()) {
	// 凡星：修复如user_center://user_center/add 识别错误的问题
	$urlArr = explode ( '://', $url );
	if (stripos ( $urlArr [0], '_' ) !== false) {
		$addons = $urlArr [0];
		$url = 'http://' . $urlArr [1];
	}
	$url = parse_url ( $url );
	$case = C ( 'URL_CASE_INSENSITIVE' );
	! $addons || $url ['scheme'] = $addons;
	$addons = $case ? parse_name ( $url ['scheme'] ) : $url ['scheme'];
	$controller = $case ? parse_name ( $url ['host'] ) : $url ['host'];
	$action = trim ( $case ? strtolower ( $url ['path'] ) : $url ['path'], '/' );

	/* 解析URL带的参数 */
	if (isset ( $url ['query'] )) {
		parse_str ( $url ['query'], $query );
		$param = array_merge ( $query, $param );
	}

	/* 基础参数 */
	$params = array (
			'_addons' => ucfirst ( $addons ),
			'_controller' => ucfirst ( $controller ),
			'_action' => $action
	);
	$params = array_merge ( $params, $param ); // 添加额外参数

	return U ( 'Home/Addons/execute', $params );
	// $qurl = $addons == $controller ? "/ad/$addons/$action" : "/ad/$addons/$controller/$action";
	// return U ($qurl , $param );
}

/**
 * 时间戳格式化
 *
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
	if (empty ( $time ))
		return '';

	$time = $time === NULL ? NOW_TIME : intval ( $time );
	return date ( $format, $time );
}
function day_format($time = NULL) {
	return time_format ( $time, 'Y-m-d' );
}
function hour_format($time = NULL) {
	return time_format ( $time, 'H:i' );
}
/**
 * 根据用户ID获取用户名
 *
 * @param integer $uid
 *        	用户ID
 * @return string 用户名
 */
function get_username($uid = 0) {
	static $list;
	if (! ($uid && is_numeric ( $uid ))) { // 获取当前登录用户名
		return session ( 'user_auth.username' );
	}

	/* 获取缓存数据 */
	if (empty ( $list )) {
		$list = S ( 'sys_active_user_list' );
	}

	/* 查找用户信息 */
	$key = "u{$uid}";
	if (isset ( $list [$key] )) { // 已缓存，直接使用
		$name = $list [$key];
	} else { // 调用接口获取用户信息
		$User = new User\Api\UserApi ();
		$info = $User->info ( $uid );
		if ($info && isset ( $info [1] )) {
			$name = $list [$key] = $info [1];
			/* 缓存用户 */
			$count = count ( $list );
			$max = C ( 'USER_MAX_CACHE' );
			while ( $count -- > $max ) {
				array_shift ( $list );
			}
			S ( 'sys_active_user_list', $list );
		} else {
			$name = '';
		}
	}
	return $name;
}

/**
 * 根据用户ID获取用户昵称
 *
 * @param integer $uid
 *        	用户ID
 * @return string 用户昵称
 */
function get_nickname($uid = 0) {
	$info = get_mult_userinfo ( $uid );
	return $info ['nickname'];
}
function get_truename($uid) {
	$info = D ( 'Home/Member' )->getMemberInfo ( $uid );
	return $info ['truename'];
}
function get_memberinfo($uid) {
	return D ( 'Home/Member' )->getMemberInfo ( $uid );
}
function get_followinfo($id) {
	return D ( 'Common/Follow' )->getFollowInfo ( $id );
}
function get_mult_userinfo($uid) {
	$info = get_followinfo ( $uid );
	if (! $info) {
		$info = get_memberinfo ( $uid );
	}
	return $info;
}
function get_mult_username($uids) {
	is_array ( $uids ) || $uids = explode ( ',', $uids );

	$uids = array_filter ( $uids );
	if (empty ( $uids )) {
		return;
	}

	foreach ( $uids as $uid ) {
		$name = get_truename ( $uid );
		if ($name) {
			$nameArr [] = $name;
		}
	}

	return implode ( ', ', $nameArr );
}
/**
 * 获取分类信息并缓存分类
 *
 * @param integer $id
 *        	分类ID
 * @param string $field
 *        	要获取的字段名
 * @return string 分类信息
 */
function get_category($id, $field = null) {
	static $list;

	/* 非法分类ID */
	if (empty ( $id ) || ! is_numeric ( $id )) {
		return '';
	}

	/* 读取缓存数据 */
	if (empty ( $list )) {
		$list = S ( 'sys_category_list' );
	}

	/* 获取分类名称 */
	if (! isset ( $list [$id] )) {
		$cate = M ( 'Category' )->find ( $id );
		if (! $cate || 1 != $cate ['status']) { // 不存在分类，或分类被禁用
			return '';
		}
		$list [$id] = $cate;
		S ( 'sys_category_list', $list ); // 更新缓存
	}
	return is_null ( $field ) ? $list [$id] : $list [$id] [$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id) {
	return get_category ( $id, 'name' );
}

/* 根据ID获取分类名称 */
function get_category_title($id) {
	return get_category ( $id, 'title' );
}

/**
 * 获取顶级模型信息
 */
function get_top_model($model_id = null) {
	$map = array (
			'status' => 1,
			'extend' => 0
	);
	if (! is_null ( $model_id )) {
		$map ['id'] = array (
				'neq',
				$model_id
		);
	}
	$model = M ( 'Model' )->where ( $map )->field ( true )->select ();
	foreach ( $model as $value ) {
		$list [$value ['id']] = $value;
	}
	return $list;
}

/**
 * 获取文档模型信息
 *
 * @param integer $id
 *        	模型ID
 * @param string $field
 *        	模型字段
 * @return array
 */
function get_document_model($id = null, $field = null) {
	static $list;

	/* 非法分类ID */
	if (! (is_numeric ( $id ) || is_null ( $id ))) {
		return '';
	}

	/* 读取缓存数据 */
	if (empty ( $list )) {
		$list = S ( 'DOCUMENT_MODEL_LIST' );
	}

	/* 获取模型名称 */
	if (empty ( $list )) {
		$map = array (
				'status' => 1,
				'extend' => 1
		);
		$model = M ( 'Model' )->where ( $map )->field ( true )->select ();
		foreach ( $model as $value ) {
			$list [$value ['id']] = $value;
		}
		S ( 'DOCUMENT_MODEL_LIST', $list ); // 更新缓存
	}

	/* 根据条件返回数据 */
	if (is_null ( $id )) {
		return $list;
	} elseif (is_null ( $field )) {
		return $list [$id];
	} else {
		return $list [$id] [$field];
	}
}

/**
 * 解析UBB数据
 *
 * @param string $data
 *        	UBB字符串
 * @return string 解析为HTML的数据
 * @author chandler_qjw
 */
function ubb($data) {
	// TODO: 待完善，目前返回原始数据
	return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 *
 * @param string $action
 *        	行为标识
 * @param string $model
 *        	触发行为的模型名
 * @param int $record_id
 *        	触发行为的记录id
 * @param int $user_id
 *        	执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null) {

	// 参数检查
	if (empty ( $action ) || empty ( $model ) || empty ( $record_id )) {
		return '参数不能为空';
	}
	if (empty ( $user_id )) {
		$user_id = is_login ();
	}

	// 查询行为,判断是否执行
	$action_info = M ( 'Action' )->getByName ( $action );
	if ($action_info ['status'] != 1) {
		return '该行为被禁用或删除';
	}

	// 插入行为日志
	$data ['action_id'] = $action_info ['id'];
	$data ['user_id'] = $user_id;
	$data ['action_ip'] = ip2long ( get_client_ip () );
	$data ['model'] = $model;
	$data ['record_id'] = $record_id;
	$data ['create_time'] = NOW_TIME;

	// 解析日志规则,生成日志备注
	if (! empty ( $action_info ['log'] )) {
		if (preg_match_all ( '/\[(\S+?)\]/', $action_info ['log'], $match )) {
			$log ['user'] = $user_id;
			$log ['record'] = $record_id;
			$log ['model'] = $model;
			$log ['time'] = NOW_TIME;
			$log ['data'] = array (
					'user' => $user_id,
					'model' => $model,
					'record' => $record_id,
					'time' => NOW_TIME
			);
			foreach ( $match [1] as $value ) {
				$param = explode ( '|', $value );
				if (isset ( $param [1] )) {
					$replace [] = call_user_func ( $param [1], $log [$param [0]] );
				} else {
					$replace [] = $log [$param [0]];
				}
			}
			$data ['remark'] = str_replace ( $match [0], $replace, $action_info ['log'] );
		} else {
			$data ['remark'] = $action_info ['log'];
		}
	} else {
		// 未定义日志规则，记录操作url
		$data ['remark'] = '操作url：' . $_SERVER ['REQUEST_URI'];
	}

	M ( 'ActionLog' )->add ( $data );

	if (! empty ( $action_info ['rule'] )) {
		// 解析行为
		$rules = parse_action ( $action, $user_id );

		// 执行行为
		$res = execute_action ( $rules, $action_info ['id'], $user_id );
	}
}

/**
 * 解析行为规则
 * 规则定义 table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 * field->要操作的字段；
 * condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 * rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 * cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 * max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 *
 * @param string $action
 *        	行为id或者name
 * @param int $self
 *        	替换规则里的变量为执行用户的id
 * @return boolean array: ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self) {
	if (empty ( $action )) {
		return false;
	}

	// 参数支持id或者name
	if (is_numeric ( $action )) {
		$map = array (
				'id' => $action
		);
	} else {
		$map = array (
				'name' => $action
		);
	}

	// 查询行为信息
	$info = M ( 'Action' )->where ( $map )->find ();
	if (! $info || $info ['status'] != 1) {
		return false;
	}

	// 解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
	$rules = $info ['rule'];
	$rules = str_replace ( '{$self}', $self, $rules );
	$rules = explode ( ';', $rules );
	$return = array ();
	foreach ( $rules as $key => &$rule ) {
		$rule = explode ( '|', $rule );
		foreach ( $rule as $k => $fields ) {
			$field = empty ( $fields ) ? array () : explode ( ':', $fields );
			if (! empty ( $field )) {
				$return [$key] [$field [0]] = $field [1];
			}
		}
		// cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
		if (! array_key_exists ( 'cycle', $return [$key] ) || ! array_key_exists ( 'max', $return [$key] )) {
			unset ( $return [$key] ['cycle'], $return [$key] ['max'] );
		}
	}

	return $return;
}

/**
 * 执行行为
 *
 * @param array $rules
 *        	解析后的规则数组
 * @param int $action_id
 *        	行为id
 * @param array $user_id
 *        	执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null) {
	if (! $rules || empty ( $action_id ) || empty ( $user_id )) {
		return false;
	}

	$return = true;
	foreach ( $rules as $rule ) {

		// 检查执行周期
		$map = array (
				'action_id' => $action_id,
				'user_id' => $user_id
		);
		$map ['create_time'] = array (
				'gt',
				NOW_TIME - intval ( $rule ['cycle'] ) * 3600
		);
		$exec_count = M ( 'ActionLog' )->where ( $map )->count ();
		if ($exec_count > $rule ['max']) {
			continue;
		}

		// 执行数据库操作
		$Model = M ( ucfirst ( $rule ['table'] ) );
		$field = $rule ['field'];
		$res = $Model->where ( $rule ['condition'] )->setField ( $field, array (
				'exp',
				$rule ['rule']
		) );

		if (! $res) {
			$return = false;
		}
	}
	return $return;
}

// 基于数组创建目录和文件
function create_dir_or_files($files) {
	foreach ( $files as $key => $value ) {
		if (substr ( $value, - 1 ) == '/') {
			mkdir ( $value );
		} else {
			@file_put_contents ( $value, '' );
		}
	}
}

if (! function_exists ( 'array_column' )) {
	function array_column(array $input, $columnKey, $indexKey = null) {
		$result = array ();
		if (null === $indexKey) {
			if (null === $columnKey) {
				$result = array_values ( $input );
			} else {
				foreach ( $input as $row ) {
					$result [] = $row [$columnKey];
				}
			}
		} else {
			if (null === $columnKey) {
				foreach ( $input as $row ) {
					$result [$row [$indexKey]] = $row;
				}
			} else {
				foreach ( $input as $row ) {
					$result [$row [$indexKey]] = $row [$columnKey];
				}
			}
		}
		return $result;
	}
}

/**
 * 获取表名（不含表前缀）
 *
 * @param string $model_id
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null) {
	if (empty ( $model_id )) {
		return false;
	}
	$Model = M ( 'Model' );
	$name = '';
	$info = $Model->getById ( $model_id );
	if ($info ['extend'] != 0) {
		$name = $Model->getFieldById ( $info ['extend'], 'name' ) . '_';
	}
	$name .= $info ['name'];
	return $name;
}

/**
 * 获取属性信息并缓存
 *
 * @param integer $id
 *        	属性ID
 * @param string $field
 *        	要获取的字段名
 * @return string 属性信息
 */
function get_model_attribute($model_id, $group = true) {
	static $list;

	/* 非法ID */
	if (empty ( $model_id ) || ! is_numeric ( $model_id )) {
		return '';
	}

	/* 获取属性 */
	if (! isset ( $list [$model_id] )) {
		$map = array (
				'model_id' => $model_id
		);
		$extend = M ( 'Model' )->getFieldById ( $model_id, 'extend' );

		if ($extend) {
			$map = array (
					'model_id' => array (
							"in",
							array (
									$model_id,
									$extend
							)
					)
			);
		}
		$info = M ( 'Attribute' )->where ( $map )->select ();
		$list [$model_id] = $info;
	}

	$attr = array ();
	foreach ( $list [$model_id] as $value ) {
		$attr [$value ['name']] = $value;
	}

	if ($group) {
		$sort = M ( 'Model' )->getFieldById ( $model_id, 'field_sort' );

		if (empty ( $sort )) { // 未排序
			$group = array (
					1 => array_merge ( $attr )
			);
		} else {
			$group = json_decode ( $sort, true );

			$keys = array_keys ( $group );
			foreach ( $group as $k => $value ) {
				foreach ( $value as $key => $val ) {
					unset ( $value [$key] );
					$value [$val] = $attr [$val];
					unset ( $attr [$val] );
				}
				$group [$k] = $value;
			}

			if (! empty ( $attr )) {
				$group [$keys [0]] = array_merge ( $group [$keys [0]], $attr );
			}
		}
		$attr = $group;
	}

	return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5'); 调用Admin模块的User接口
 *
 * @param string $name
 *        	格式 [模块名]/接口名/方法名
 * @param array|string $vars
 *        	参数
 */
function api($name, $vars = array()) {
	$array = explode ( '/', $name );
	$method = array_pop ( $array );
	$classname = array_pop ( $array );
	$module = $array ? array_pop ( $array ) : 'Common';
	$callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
	if (is_string ( $vars )) {
		parse_str ( $vars, $vars );
	}
	return call_user_func_array ( $callback, $vars );
}

/**
 * 根据条件字段获取指定表的数据
 *
 * @param mixed $value
 *        	条件，可用常量或者数组
 * @param string $condition
 *        	条件字段
 * @param string $field
 *        	需要返回的字段，不传则返回整个数据
 * @param string $table
 *        	需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null) {
	if (empty ( $value ) || empty ( $table )) {
		return false;
	}

	// 拼接参数
	$map [$condition] = $value;
	$info = M ( ucfirst ( $table ) )->where ( $map );
	if (empty ( $field )) {
		$info = $info->field ( true )->find ();
	} else {
		$info = $info->getField ( $field );
	}
	return $info;
}

/**
 * 获取链接信息
 *
 * @param int $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author huajie <banhuajie@163.com>
 */
function get_link($link_id = null, $field = 'url') {
	$link = '';
	if (empty ( $link_id )) {
		return $link;
	}
	$link = M ( 'Url' )->getById ( $link_id );
	if (empty ( $field )) {
		return $link;
	} else {
		return $link [$field];
	}
}

/**
 * 获取文档封面图片
 *
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据 或者 指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null) {
	if (empty ( $cover_id ))
		return false;

	$map ['status'] = 1;
	$picture = M ( 'Picture' )->where ( $map )->getById ( $cover_id );

	if (empty ( $picture ))
		return '';

	return empty ( $field ) ? $picture : $picture [$field];
}
function get_cover_url($cover_id) {
	$url = get_cover ( $cover_id, 'path' );
	if (empty ( $url ))
		return '';

	return SITE_URL . $url;
}
// 兼容旧方法
function get_picture_url($cover_id) {
	return get_cover_url ( $cover_id );
}
function get_img_html($cover_id) {
	$url = get_cover_url ( $cover_id );

	if (empty ( $url ))
		return '';

	return '<img class="list_img" src="' . $url . '" >';
}
/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 *
 * @param number $pos
 *        	推荐位的值
 * @param number $contain
 *        	指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0) {
	if (empty ( $pos ) || empty ( $contain )) {
		return false;
	}

	// 将两个参数进行按位与运算，不为0则表示$contain属于$pos
	$res = $pos & $contain;
	if ($res !== 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * 获取数据的所有子孙数据的id值
 *
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */
function get_stemma($pids, Model &$model, $field = 'id') {
	$collection = array ();

	// 非空判断
	if (empty ( $pids )) {
		return $collection;
	}

	if (is_array ( $pids )) {
		$pids = trim ( implode ( ',', $pids ), ',' );
	}
	$result = $model->field ( $field )->where ( array (
			'pid' => array (
					'IN',
					( string ) $pids
			)
	) )->select ();
	$child_ids = array_column ( ( array ) $result, 'id' );

	while ( ! empty ( $child_ids ) ) {
		$collection = array_merge ( $collection, $result );
		$result = $model->field ( $field )->where ( array (
				'pid' => array (
						'IN',
						$child_ids
				)
		) )->select ();
		$child_ids = array_column ( ( array ) $result, 'id' );
	}
	return $collection;
}

/**
 * 判断关键词是否唯一
 *
 * @author weiphp
 */
function keyword_unique($keyword) {
	if (empty ( $keyword ))
		return false;

	$map ['keyword'] = $keyword;
	$info = M ( 'keyword' )->where ( $map )->find ();
	return empty ( $info );
}
// 分析枚举类型配置值 格式 a:名称1,b:名称2
// weiphp 该函数是从admin的function的文件里提取这到里
function parse_config_attr($string) {
	$array = preg_split ( '/[;\r\n]+/', trim ( $string, ",;\r\n" ) );
	if (strpos ( $string, ':' )) {
		$value = array ();
		foreach ( $array as $val ) {
			list ( $k, $v ) = explode ( ':', $val );
			$value [$k] = $v;
		}
	} else {
		$value = $array;
	}
	foreach ( $value as &$vo ) {
		$vo = clean_hide_attr ( $vo );
	}
	// dump($value);
	return $value;
}
function clean_hide_attr($str) {
	$arr = explode ( '|', $str );
	return $arr [0];
}
function get_hide_attr($str) {
	$arr = explode ( '|', $str );
	return $arr [1];
}
// 分析枚举类型字段值 格式 a:名称1,b:名称2
// 暂时和 parse_config_attr功能相同
// 但请不要互相使用，后期会调整
function parse_field_attr($string) {
	if (0 === strpos ( $string, ':' )) {
		// 采用函数定义
		return eval ( substr ( $string, 1 ) . ';' );
	}
	$array = preg_split ( '/[;\r\n]+/', trim ( $string, ",;\r\n" ) );
	// dump($array);
	if (strpos ( $string, ':' )) {
		$value = array ();
		foreach ( $array as $val ) {
			list ( $k, $v ) = explode ( ':', $val );
			empty ( $v ) && $v = $k;
			$k = clean_hide_attr ( $k );
			$value [$k] = $v;
		}
	} else {
		$value = $array;
	}
	// dump($value);
	return $value;
}
/* 解析列表定义规则 */
function get_list_field($data, $grid, $model) {
	// 获取当前字段数据
	foreach ( $grid ['field'] as $field ) {
		$array = explode ( '|', $field );
		$array [0] = trim ( $array [0] );
		$temp = $data [$array [0]];
		// 函数支持
		if (isset ( $array [1] )) {
			if ($array [1] == 'get_name_by_status') {
				$temp = get_name_by_status ( $temp, $array [0], $model ['id'] );
			} else {
				$temp = call_user_func ( $array [1], $temp );
			}
		}
		$data2 [$array [0]] = $temp;
	}
	if (! empty ( $grid ['format'] )) {
		$value = preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use($data2) {
			return $data2 [$match [1]];
		}, $grid ['format'] );
	} else {
		$value = implode ( ' ', $data2 );
	}

	// 链接支持
	if (! empty ( $grid ['href'] )) {
		$links = explode ( ',', $grid ['href'] );
		foreach ( $links as $link ) {
			$array = explode ( '|', $link );
			$href = $array [0];
			if (preg_match ( '/^\[([a-z_]+)\]$/', $href, $matches )) {
				$val [] = $data2 [$matches [1]];
			} else {
				$show = isset ( $array [1] ) ? $array [1] : $value;
				// 增加跳转方式处理 weiphp
				$target = '_self';
				if (preg_match ( '/target=(\w+)/', $href, $matches )) {
					$target = $matches [1];
					$href = str_replace ( '&' . $matches [0], '', $href );
				}

				// 替换系统特殊字符串
				$href = str_replace ( array (
						'[DELETE]',
						'[EDIT]',
						'[MODEL]'
				), array (
						'del?id=[id]&model=[MODEL]',
						'edit?id=[id]&model=[MODEL]',
						$model ['id']
				), $href );

				// 替换数据变量
				$href = preg_replace_callback ( '/\[([a-z_]+)\]/', function ($match) use($data) {
					return $data [$match [1]];
				}, $href );

				// 兼容多种写法
				if (strpos ( $href, '?' ) === false && strpos ( $href, '&' ) !== false) {
					$href = preg_replace ( "/&/i", "?", $href, 1 );
				}
				if ($show == '删除') {
					$val [] = '<a class="confirm"   href="' . urldecode ( U ( $href, $GLOBALS ['get_param'] ) ) . '">' . $show . '</a>';
				} else {
					$val [] = '<a  target="' . $target . '" href="' . urldecode ( U ( $href, $GLOBALS ['get_param'] ) ) . '">' . $show . '</a>';
				}
			}
		}
		$value = implode ( ' ', $val );
	}
	return $value;
}
/**
 * 获取状态值对应的标题
 *
 * @author weiphp
 */
function get_name_by_status($val, $name, $model_id) {
	static $_name = array ();
	if (! isset ( $_name [$model_id] )) {
		$_name [$model_id] = array ();
		$map ['extra'] = array (
				'EXP',
				'!=""'
		);
		$map ['model_id'] = $model_id;
		$list = M ( 'attribute' )->where ( $map )->select ();
		foreach ( $list as $attr ) {
			if (empty ( $attr ['extra'] ))
				continue;

			$extra = parse_config_attr ( $attr ['extra'] );
			if (is_array ( $extra ) && ! empty ( $extra )) {
				$_name [$model_id] [$attr ['name']] ['value'] = $extra;
				$_name [$model_id] [$attr ['name']] ['type'] = $attr ['type'];
			}
		}
	}

	if ($_name [$model_id] [$name] ['type'] == 'checkbox') {
		$valArr = explode ( ',', $val );
		foreach ( $valArr as $v ) {
			$res [] = empty ( $_name [$model_id] [$name] ['value'] [$v] ) ? $v : $_name [$model_id] [$name] ['value'] [$v];
		}

		return implode ( ', ', $res );
	} else {
		return empty ( $_name [$model_id] [$name] ['value'] [$val] ) ? $val : $_name [$model_id] [$name] ['value'] [$val];
	}
}
function addWeixinLog($data, $data_post = '') {
	$log ['cTime'] = time ();
	$log ['cTime_format'] = date ( 'Y-m-d H:i:s', $log ['cTime'] );
	$log ['data'] = is_array ( $data ) ? serialize ( $data ) : $data;
	$log ['data_post'] = is_array ( $data_post ) ? serialize ( $data_post ) : $data_post;
	M ( 'weixin_log' )->add ( $log );
}
/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 *
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称
 * @return 返回新的一维数组
 */
function getSubByKey($pArray, $pKey = "", $pCondition = "") {
	$result = array ();
	if (is_array ( $pArray )) {
		foreach ( $pArray as $temp_array ) {
			if (is_object ( $temp_array )) {
				$temp_array = ( array ) $temp_array;
			}
			if (("" != $pCondition && $temp_array [$pCondition [0]] == $pCondition [1]) || "" == $pCondition) {
				$result [] = ("" == $pKey) ? $temp_array : isset ( $temp_array [$pKey] ) ? $temp_array [$pKey] : "";
			}
		}
		return $result;
	} else {
		return false;
	}
}
// 判断是否是在微信浏览器里
function isWeixinBrowser() {
	$agent = $_SERVER ['HTTP_USER_AGENT'];
	if (! strpos ( $agent, "icroMessenger" )) {
		return false;
	}
	return true;
}
// php获取当前访问的完整url地址
function GetCurUrl() {
	$url = 'http://';
	if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
		$url = 'https://';
	}
	if ($_SERVER ['SERVER_PORT'] != '80') {
		$url .= $_SERVER ['HTTP_HOST'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
	} else {
		$url .= $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
	}
	// 兼容后面的参数组装
	if (stripos ( $url, '?' ) === false) {
		$url .= '?t=' . time ();
	}
	return $url;
}
// 获取当前用户的OpenId
function get_openid($openid = NULL) {
	$token = get_token ();
	if ($openid !== NULL) {
		session ( 'openid_' . $token, $openid );
	} elseif (! empty ( $_REQUEST ['openid'] )) {
		session ( 'openid_' . $token, $_REQUEST ['openid'] );
	}
	$openid = session ( 'openid_' . $token );

	$isWeixinBrowser = isWeixinBrowser ();
	if (empty ( $openid ) && $isWeixinBrowser) {
		$callback = GetCurUrl ();
		OAuthWeixin ( $callback );
	}

	if (empty ( $openid )) {
		return - 1;
	}

	return $openid;
}
// 获取当前用户的Token
function get_token($token = NULL) {
	if ($token !== NULL) {
		session ( 'token', $token );
	} elseif (! empty ( $_REQUEST ['token'] )) {
		session ( 'token', $_REQUEST ['token'] );
	}
	$token = session ( 'token' );

	if (empty ( $token )) {
		return - 1;
	}

	return $token;
}
// 获取当前用户的UID,方便在模型里的自动填充功能使用
function get_mid() {
	return session ( 'mid' );
}
// 通过openid获取微信用户基本信息,此功能只有认证的服务号才能用
function getWeixinUserInfo($openid, $token) {
	$access_token = get_access_token ( $token );
	if (empty ( $access_token )) {
		return false;
	}

	$param2 ['access_token'] = $access_token;
	$param2 ['openid'] = $openid;
	$param2 ['lang'] = 'zh_CN';

	$url = 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query ( $param2 );
	$content = file_get_contents ( $url );
	$content = json_decode ( $content, true );
	return $content;
}
// 获取公众号的信息
function get_token_appinfo($token = '') {
	empty ( $token ) && $token = get_token ();
	$map ['token'] = $token;
	$info = M ( 'member_public' )->where ( $map )->find ();
	return $info;
}
// 判断公众号的类型：是订阅号还是服务号
function get_token_type($token = '') {
	$info = get_token_appinfo ( $token );
	return intval ( $info ['type'] );
}

// 获取access_token，自动带缓存功能
function get_access_token($token = '') {
	empty ( $token ) && $token = get_token ();
	$key = 'access_token_' . $token;
	$res = S ( $key );
	if ($res !== false){
		return $res;
	}
		

	$info = get_token_appinfo ( $token );
	if (empty ( $info ['appid'] ) || empty ( $info ['secret'] )) {
		S ( $key, 0, 7200 );
		return 0;
	}

	//$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $info ['appid'] . '&secret=' . $info ['secret'];
	$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . C('APPID') . '&secret=' . C('AppSecret');
	$tempArr = json_decode ( file_get_contents ( $url ), true );
	if (@array_key_exists ( 'access_token', $tempArr )) {
		S ( $key, $tempArr ['access_token'], 7200 );
		return $tempArr ['access_token'];
	} else {
		return 0;
	}
}
function OAuthWeixin($callback) {
	$isWeixinBrowser = isWeixinBrowser ();
	$info = get_token_appinfo ();
	if (! $isWeixinBrowser || $info ['type'] != 2 || empty ( $info ['appid'] )) {
		redirect ( $callback . '&openid=-1' );
	}
	$param ['appid'] = $info ['appid'];

	if (! isset ( $_GET ['getOpenId'] )) {
		$param ['redirect_uri'] = $callback . '&getOpenId=1';
		$param ['response_type'] = 'code';
		$param ['scope'] = 'snsapi_base';
		$param ['state'] = 123;
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query ( $param ) . '#wechat_redirect';
		redirect ( $url );
	} elseif ($_GET ['state']) {
		$param ['secret'] = $info ['secret'];
		$param ['code'] = I ( 'code' );
		$param ['grant_type'] = 'authorization_code';

		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query ( $param );
		$content = file_get_contents ( $url );
		$content = json_decode ( $content, true );
		redirect ( $callback . '&openid=' . $content ['openid'] );
	}
}
/**
 * 执行SQL文件
 */
function execute_sql_file($sql_path) {
	// 读取SQL文件
	$sql = wp_file_get_contents ( $sql_path );
	$sql = str_replace ( "\r", "\n", $sql );
	$sql = explode ( ";\n", $sql );

	// 替换表前缀
	$orginal = 'wp_';
	$prefix = C ( 'DB_PREFIX' );
	$sql = str_replace ( "{$orginal}", "{$prefix}", $sql );

	// 开始安装
	foreach ( $sql as $value ) {
		$value = trim ( $value );
		if (empty ( $value ))
			continue;

		$res = M ()->execute ( $value );
		// dump($res);
		// dump(M()->getLastSql());
	}
}
// 设置微信关联聊天中用到的用户状态
function set_user_status($addon, $keywordArr = array()) {
	// 设置用户状态
	$user_status ['addon'] = $addon;
	$user_status ['keywordArr'] = $keywordArr;

	$openid = get_openid ();
	return S ( 'user_status_' . $openid, $user_status );
}

// 获取公众号等级名
function get_public_group_name($group_id) {
	static $_public_group_name;

	$group_id = intval ( $group_id );
	if (! isset ( $_public_group_name [$group_id] )) {
		$group_list = M ( 'member_public_group' )->field ( 'id, title' )->select ();
		foreach ( $group_list as $g ) {
			$_public_group_name [$g ['id']] = $g ['title'];
		}
		$_public_group_name [0] = '无';
	}

	return $_public_group_name [$group_id];
}

// 截取内容
function getShort($str, $length = 40, $ext = '') {
	$str = htmlspecialchars ( $str );
	$str = strip_tags ( $str );
	$str = htmlspecialchars_decode ( $str );
	$strlenth = 0;
	$out = '';
	preg_match_all ( "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match );
	foreach ( $match [0] as $v ) {
		preg_match ( "/[\xe0-\xef][\x80-\xbf]{2}/", $v, $matchs );
		if (! empty ( $matchs [0] )) {
			$strlenth += 1;
		} elseif (is_numeric ( $v )) {
			$strlenth += 0.5; // 字符字节长度比例 汉字为1
		} else {
			$strlenth += 0.5; // 字符字节长度比例 汉字为1
		}

		if ($strlenth > $length) {
			$output .= $ext;
			break;
		}

		$output .= $v;
	}
	return $output;
}

// 防超时的file_get_contents改造函数
function wp_file_get_contents($url) {
	$context = stream_context_create ( array (
			'http' => array (
					'timeout' => 30
			)
	) ) // 超时时间，单位为秒

	;

	return file_get_contents ( $url, 0, $context );
}

// 全局的安全过滤函数
function safe($text, $type = 'html') {
	// 无标签格式
	$text_tags = '';
	// 只保留链接
	$link_tags = '<a>';
	// 只保留图片
	$image_tags = '<img>';
	// 只存在字体样式
	$font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
	// 标题摘要基本格式
	$base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
	// 兼容Form格式
	$form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
	// 内容等允许HTML的格式
	$html_tags = $base_tags . '<meta><ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
	// 全HTML格式
	$all_tags = $form_tags . $html_tags . '<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
	// 过滤标签
	$text = html_entity_decode ( $text, ENT_QUOTES, 'UTF-8' );
	$text = strip_tags ( $text, ${$type . '_tags'} );

	// 过滤攻击代码
	if ($type != 'all') {
		// 过滤危险的属性，如：过滤on事件lang js
		while ( preg_match ( '/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|background|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat ) ) {
			$text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
		}
		while ( preg_match ( '/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat ) ) {
			$text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
		}
	}
	return $text;
}
// 创建多级目录
function mkdirs($dir) {
	if (! is_dir ( $dir )) {
		if (! mkdirs ( dirname ( $dir ) )) {
			return false;
		}
		if (! mkdir ( $dir, 0777 )) {
			return false;
		}
	}
	return true;
}
// 组装查询条件
function getIdsForMap($ids, $map = array(), $field = 'id') {
	$ids = safe ( $ids );
	$ids = preg_split ( '/[\s,;]+/', $ids ); // 支持以空格tab逗号分号分割ID
	$ids = array_filter ( $ids );
	if (empty ( $ids ))
		return $map;

	$map [$field] = array (
			'in',
			$ids
	);

	return $map;
}
// 获取通用分类级联菜单的标题，改方法仅支持级联的数据源配置成数据表common_category的情况，其它情况需要使用下面的getCascadeTitle方法
function getCommonCategoryTitle($ids) {
	$extra = 'type=db&table=common_category';

	return getCascadeTitle ( $ids, $extra );
}
// 获取级联菜单的标题的通用处理方法
function getCascadeTitle($ids, $extra) {
	$idArr = explode ( ',', $ids );
	$idArr = array_filter ( $idArr );
	if (empty ( $idArr ))
		return '';

	parse_str ( $extra, $arr );
	if ($arr ['type'] == 'db') {
		$table = $arr ['table'];
		unset ( $arr ['type'], $arr ['table'] );

		$arr ['token'] = get_token ();
		$arr ['id'] = array (
				'in',
				$idArr
		);
		$list = M ( $table )->where ( $arr )->field ( 'title' )->select ();
		$titleArr = getSubByKey ( $list, 'title' );
	} else {
		$str = str_replace ( '，', ',', $extra );
		$str = str_replace ( '【', '[', $str );
		$str = str_replace ( '】', ']', $str );
		$str = str_replace ( '：', ':', $str );

		$arr = StringToArray ( $str );
		$str = '';
		foreach ( $arr as $v ) {
			if ($v == '[' || $v == ']' || $v == ',') {
				if ($str) {
					$block = explode ( ':', trim ( $str ) );
					if (in_array ( $block [0], $idArr )) {
						$titleArr [] = isset ( $block [1] ) ? $block [1] : $block [0];
					}
				}
				$str = '';
			} else {
				$str .= $v;
			}
		}
	}
	return implode ( ' > ', $titleArr );
}
// 把字符串转成数组，支持汉字，只能是utf-8格式的
function StringToArray($str) {
	$result = array ();
	$len = strlen ( $str );
	$i = 0;
	while ( $i < $len ) {
		$chr = ord ( $str [$i] );
		if ($chr == 9 || $chr == 10 || (32 <= $chr && $chr <= 126)) {
			$result [] = substr ( $str, $i, 1 );
			$i += 1;
		} elseif (192 <= $chr && $chr <= 223) {
			$result [] = substr ( $str, $i, 2 );
			$i += 2;
		} elseif (224 <= $chr && $chr <= 239) {
			$result [] = substr ( $str, $i, 3 );
			$i += 3;
		} elseif (240 <= $chr && $chr <= 247) {
			$result [] = substr ( $str, $i, 4 );
			$i += 4;
		} elseif (248 <= $chr && $chr <= 251) {
			$result [] = substr ( $str, $i, 5 );
			$i += 5;
		} elseif (252 <= $chr && $chr <= 253) {
			$result [] = substr ( $str, $i, 6 );
			$i += 6;
		}
	}
	return $result;
}
/**
 * 增加用户积分函数
 *
 * @param string $name
 *        	积分标识名
 * @param int $lock_time
 *        	解锁时间，即多长时间内才能重复加积分，为0时不作控制
 * @param array $credit
 *        	自定义积分值，格式：array('score'=>财富值,'experience'=>经验值);为空时默认取管理中心积分管理里的配置值
 * @param int $admin_uid
 *        	管理员UID，用于管理员给用户手工加积分时的场景
 */
function add_credit($name, $lock_time = 5, $credit = array(), $admin_uid = 0) {
	if (empty ( $name ))
		return false;


	if ($lock_time > 0) {
		$key = 'credit_lock_' . get_token () . '_' . get_openid () . '_' . $name;
		if (S ( $key ))
			return false;

		S ( $key, 1, $lock_time );
	}

	$data ['credit_name'] = $name;
	$data ['admin_uid'] = $admin_uid;
	$data = array_merge ( $data, $credit );
	$credit = D ( 'Common/Credit' )->addCredit ( $data );
}
// 判断用户最大可创建的公众号数
function getPublicMax($uid) {
	$map ['uid'] = $uid;
	$public_count = M ( 'member' )->where ( $map )->getField ( 'public_count' );
	if ($public_count === NULL) {
		$public_count = C ( 'DEFAULT_PUBLIC_CREATE_MAX_NUMB' );
	}
	return intval ( $public_count );
}
function diyPage($keyword) {
	$map ['keyword'] = $keyword;
	$map ['token'] = get_token ();
	$page = M ( 'diy' )->where ( $map )->find ();

	if (! $page) {
		$map ['token'] = '0';
		$page = M ( 'diy' )->where ( $map )->find ();
	}
	// dump($page);
	if (! $page) {
		return false;
	}

	$model = A ( 'Addons://Diy/Diy' );
	// dump($model);exit;
	$model->show ( $page ['id'] );
}
// 各插件获取关联抽奖活动的地址 暂只支持刮刮卡
function event_url($addon_title, $id = '0') {
	$map ['token'] = get_token ();
	$map ['addon_condition'] = array (
			'exp',
			"='[{$addon_title}:*]' or addon_condition='[{$addon_title}:{$id}]'"
	);

	$event = M ( 'Scratch' )->where ( $map )->order ( 'id desc' )->find ();
	$event_url = '';
	if ($event) {
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();
		$param ['id'] = $event ['id'];
		$event_url = addons_url ( 'Scratch://Scratch/show', $param );
	}
	return $event_url;
}
// 抽奖或者优惠券领取的插件条件判断
function addon_condition_check($addon_condition) {
	preg_match_all ( "/\[([\s\S]*):([\*,\d]*)\]/i", $addon_condition, $match );
	if (empty ( $match [1] [0] ) || empty ( $match [2] [0] )) {
		return true;
	}

	$conditon ['token'] = get_token ();
	$conditon ['uid'] = get_mid ();
	switch ($match [1] [0]) {
		case '投票' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['vote_id'] = $match [2] [0];
			$conditon ['user_id'] = get_mid ();
			unset ( $conditon ['uid'] );
			$res = M ( 'vote_log' )->where ( $conditon )->find ();
			break;
		case '通用表单' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['forms_id'] = $match [2] [0];
			$res = M ( 'forms_value' )->where ( $conditon )->find ();
			break;
		case '微考试' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['exam_id'] = $match [2] [0];
			$res = M ( 'exam_answer' )->where ( $conditon )->find ();
			break;
		case '微测试' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['test_id'] = $match [2] [0];
			$res = M ( 'test_answer' )->where ( $conditon )->find ();
			break;
		case '微调研' :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['survey_id'] = $match [2] [0];
			$res = M ( 'survey_answer' )->where ( $conditon )->find ();
			break;
		default :
			$match [2] [0] != '*' && $match [2] [0] > 0 && $conditon ['id'] = $match [2] [0];
			$res = M ( $match [1] [0] )->where ( $conditon )->find ();
	}
	// dump ( $res );
	// dump ( M ()->getLastSql () );

	return ! empty ( $res );
}
// 抽奖或者优惠券领取的插件条件提示
function condition_tips($addon_condition) {
	if (empty ( $addon_condition ))
		return '';

	preg_match_all ( "/\[([\s\S]*):([\*,\d]*)\]/i", $addon_condition, $match );
	if (empty ( $match [1] [0] ) || empty ( $match [2] [0] )) {
		return '';
	}

	$conditon ['token'] = get_token ();
	$conditon ['id'] = $match [2] [0];
	$title = '';
	$has_title = $conditon ['id'] != '*' && $conditon ['id'] > 0;

	switch ($match [1] [0]) {
		case '投票' :
			$has_title && $title = M ( 'vote' )->where ( $conditon )->getField ( 'title' );
			break;
		case '通用表单' :
			$has_title && $title = M ( 'forms' )->where ( $conditon )->getField ( 'title' );
			break;
		case '微考试' :
			$has_title && $title = M ( 'exam' )->where ( $conditon )->getField ( 'title' );
			break;
		case '微测试' :
			$has_title && $title = M ( 'test' )->where ( $conditon )->getField ( 'title' );
			break;
		case '微调研' :
			$has_title && $title = M ( 'survey' )->where ( $conditon )->getField ( 'title' );
			break;
		default :
			$has_title && $title = M ( $match [1] [0] )->where ( $conditon )->getField ( 'title' );
	}
	$result = '需要参与' . $title . $match [1] [0] . '后才能领取';

	return $result;
}
function lastsql() {
	dump ( M ()->getLastSql () );
}
// 商业代码解密
function code_decode($text) {
	$key = substr ( C ( 'WEIPHP_STORE_LICENSE' ), 0, 5 );
	return think_decrypt ( $text, $key );
}
function outExcel($dataArr, $fileName = '', $sheet = false) {
	require_once VENDOR_PATH . 'download-xlsx.php';
	export_csv ( $dataArr, $fileName, $sheet );
	unset ( $sheet );
	unset ( $dataArr );
}
// 获取通用分类表的分类标题
function category_title($cate_id) {
	static $_category_title = array ();
	if (isset ( $_category_title [$cate_id] )) {
		return $_category_title [$cate_id];
	}

	$map ['token'] = get_token ();
	$list = M ( 'common_category' )->where ( $map )->field ( 'id,title' )->select ();
	foreach ( $list as $v ) {
		$_category_title [$v ['id']] = $v ['title'];
	}
	if (! isset ( $_category_title [$cate_id] )) {
		$_category_title [$cate_id] = '';
	}
	return $_category_title [$cate_id];
}
function get_lecturer_name($lecturer_id) {
	static $_lecturer_name = array ();
	if (isset ( $_lecturer_name [$lecturer_id] )) {
		return $_lecturer_name [$lecturer_id];
	}

	$map ['token'] = get_token ();
	$list = M ( 'classes_lecturer' )->where ( $map )->field ( 'id,name' )->select ();
	foreach ( $list as $v ) {
		$_lecturer_name [$v ['id']] = $v ['name'];
	}
	if (! isset ( $_lecturer_name [$lecturer_id] )) {
		$_lecturer_name [$lecturer_id] = '';
	}
	return $_lecturer_name [$lecturer_id];
}
function check_token_purview($table, $id, $field = 'token') {
	$token = get_token ();
	$map ['id'] = $id;
	$info = M ( $table )->where ( $map )->field ( $field )->find ();
	if ($info === false || $info [$field] == $token)
		return true; // 没有这个字段或者没有这个记录直接返回

	exit ( '非法访问' );
}
// weiphp专用分割函数，同时支持常见的按空格、逗号、分号、换行进行分割
function wp_explode($string, $delimiter = "\s,;\r\n") {
	if (empty ( $string ))
		return array ();

		// 转换中文符号
	$string = iconv ( 'utf-8', 'gbk', $string );
	$string = preg_replace ( '/\xa3([\xa1-\xfe])/e', 'chr(ord(\1)-0x80)', $string );
	$string = iconv ( 'gbk', 'utf-8', $string );

	$arr = preg_split ( '/[' . $delimiter . ']+/', $string );
	return array_unique ( array_filter ( $arr ) );
}
function get_code_img($qr_code) {
	if (! $qr_code)
		return '';

	$html = '<img src="' . $qr_code . '" width="50" height="50" />';
	return $html;
}
function get_file_title($attach_ids) {
	$ids = wp_explode ( $attach_ids );
	if (empty ( $ids ))
		return '';

	$map ['id'] = array (
			'in',
			$ids
	);
	$names = M ( 'file' )->where ( $map )->getFields ( 'name' );

	return implode ( ', ', $names );
}
// 阿拉伯数字转中文表述，如101转成一百零一
function num2cn($number) {
	$number = intval ( $number );
	$capnum = array (
			"零",
			"一",
			"二",
			"三",
			"四",
			"五",
			"六",
			"七",
			"八",
			"九"
	);
	$capdigit = array (
			"",
			"十",
			"百",
			"千",
			"万"
	);

	$data_arr = str_split ( $number );
	$count = count ( $data_arr );
	for($i = 0; $i < $count; $i ++) {
		$d = $capnum [$data_arr [$i]];
		$arr [] = $d != '零' ? $d . $capdigit [$count - $i - 1] : $d;
	}
	$cncap = implode ( "", $arr );

	$cncap = preg_replace ( "/(零)+/", "0", $cncap ); // 合并连续“零”
	$cncap = trim ( $cncap, '0' );
	$cncap = str_replace ( "0", "零", $cncap ); // 合并连续“零”
	$cncap == '一十' && $cncap = '十';
	$cncap == '' && $cncap = '零';
	// echo ( $data.' : '.$cncap.' <br/>' );
	return $cncap;
}
function week_name($number = null) {
	if ($number === null)
		$number = date ( 'w' );

	$arr = array (
			"日",
			"一",
			"二",
			"三",
			"四",
			"五",
			"六"
	);

	return '星期' . $arr [$number];
}
// 日期转换成星期几
function daytoweek($day = null) {
	$day === null && $day = date ( 'Y-m-d' );
	if (empty ( $day ))
		return '';

	$number = date ( 'w', strtotime ( $day ) );

	return week_name ( $number );
}
/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map
 *        	映射关系二维数组 array(
 *        	'字段名1'=>array(映射关系数组),
 *        	'字段名2'=>array(映射关系数组),
 *        	......
 *        	)
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array array(
 *         array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *         ....
 *         )
 *
 */
function int_to_string(&$data, $map = array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
	if ($data === false || $data === null) {
		return $data;
	}
	$data = ( array ) $data;
	foreach ( $data as $key => $row ) {
		foreach ( $map as $col => $pair ) {
			if (isset ( $row [$col] ) && isset ( $pair [$row [$col]] )) {
				$data [$key] [$col . '_text'] = $pair [$row [$col]];
			}
		}
	}
	return $data;
}
function importFormExcel($attach_id, $column) {
	$res = array (
			'status' => 0,
			'data' => ''
	);
	if (empty ( $attach_id ) || ! is_numeric ( $attach_id )) {
		$res ['data'] = '上传文件ID无效！';
		return $res;
	}
	$file = M ( 'file' )->where ( 'id=' . $attach_id )->find ();
	$root = C ( 'DOWNLOAD_UPLOAD.rootPath' );
	$filename = SITE_PATH . '/Uploads/Download/' . $file ['savepath'] . $file ['savename'];
	if (! file_exists ( $filename )) {
		$res ['data'] = '上传的文件失败';
		return $res;
	}
	$extend = $file ['ext'];
	if (! ($extend == 'xls' || $extend == 'xlsx')) {
		$res ['data'] = '文件格式不对，请上传xls,xlsx格式的文件';
		return $res;
	}

	vendor ( 'PHPExcel' );
	vendor ( 'PHPExcel.PHPExcel_IOFactory' );
	vendor ( 'PHPExcel.Reader.Excel5' );

	$format = strtolower ( $extend ) == 'xls' ? 'Excel5' : 'excel2007';
	$objReader = \PHPExcel_IOFactory::createReader ( $format );
	$objPHPExcel = $objReader->load ( $filename );
	$objPHPExcel->setActiveSheetIndex ( 0 );
	$sheet = $objPHPExcel->getSheet ( 0 );
	$highestRow = $sheet->getHighestRow (); // 取得总行数

	for($j = 2; $j <= $highestRow; $j ++) {
		$addData = array ();
		foreach ( $column as $k => $v ) {
			$addData [$v] = trim ( ( string ) $objPHPExcel->getActiveSheet ()->getCell ( $k . $j )->getValue () );
		}

		$isempty = true;
		foreach ( $column as $v ) {
			$isempty && $isempty = empty ( $addData [$v] );
		}

		if (! $isempty)
			$result [$j] = $addData;
	}

	$res ['status'] = 1;
	$res ['data'] = $result;
	return $res;
}
function showNewIcon($time, $day = 3) {
	$img = '';
	if (NOW_TIME < ($time + 86400 * $day)) {
		$img = '<img src="' . C ( 'TMPL_PARSE_STRING.__IMG__' ) . '/new.png"/>';
	}
	return $img;
}
function replace_url($content) {
	$param ['token'] = get_token ();
	$param ['openid'] = get_openid ();

	$sreach = array (
			'[follow]',
			'[website]',
			'[token]',
			'[openid]'
	);
	$replace = array (
			addons_url ( 'UserCenter://UserCenter/bind', $param ),
			addons_url ( 'WeiSite://WeiSite/index', $param ),
			$param ['token'],
			$param ['openid']
	);
	$content = str_replace ( $sreach, $replace, $content );

	return $content;
}
/**
 * 验证分类是否允许发布内容
 *
 * @param integer $id
 *        	分类ID
 * @return boolean true-允许发布内容，false-不允许发布内容
 */
function check_category($id) {
	if (is_array ( $id )) {
		$type = get_category ( $id ['category_id'], 'type' );
		$type = explode ( ",", $type );
		return in_array ( $id ['type'], $type );
	} else {
		$publish = get_category ( $id, 'allow_publish' );
		return $publish ? true : false;
	}
}

/**
 * 检测分类是否绑定了指定模型
 *
 * @param array $info
 *        	模型ID和分类ID数组
 * @return boolean true-绑定了模型，false-未绑定模型
 */
function check_category_model($info) {
	$cate = get_category ( $info ['category_id'] );
	$array = explode ( ',', $info ['pid'] ? $cate ['model_sub'] : $cate ['model'] );
	return in_array ( $info ['model_id'], $array );
}
// 获取随机的字符串，用于token，EncodingAESKey等的生成
function get_rand_char($length = 6) {
	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$strLength = 61;

	for($i = 0; $i < $length; $i ++) {
		$res .= $str [rand ( 0, $strLength )];
	}

	return $res;
}
/**
 * 根据两点间的经纬度计算距离
 *
 * @param float $lat
 *        	纬度值
 * @param float $lng
 *        	经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
	$earthRadius = 6367000; // approximate radius of earth in meters

	// Convert these degrees to radians to work with the formula
	$lat1 = ($lat1 * pi ()) / 180;
	$lng1 = ($lng1 * pi ()) / 180;

	$lat2 = ($lat2 * pi ()) / 180;
	$lng2 = ($lng2 * pi ()) / 180;

	// Using the Haversine formula http://en.wikipedia.org/wiki/Haversine_formula calculate the distance

	$calcLongitude = $lng2 - $lng1;
	$calcLatitude = $lat2 - $lat1;
	$stepOne = pow ( sin ( $calcLatitude / 2 ), 2 ) + cos ( $lat1 ) * cos ( $lat2 ) * pow ( sin ( $calcLongitude / 2 ), 2 );
	$stepTwo = 2 * asin ( min ( 1, sqrt ( $stepOne ) ) );
	$calculatedDistance = $earthRadius * $stepTwo;

	return round ( $calculatedDistance );
}
/**
 * 短链接功能
 *
 * @param float $long_url
 *        	长链接
 * @return string 如果没有微信短链接接口权限或者不成功，就原样返回长链接，否则返回短链接
 */
function short_url($long_url) {
	$access_token = get_access_token ( $token );
	if (empty ( $access_token )) {
		return $long_url;
	}

	$url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=' . $access_token;

	$data ['action'] = 'long2short';
	$data ['long_url'] = $long_url;

	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
	curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	$res = curl_exec ( $ch );
	curl_close ( $ch );
	$res = json_decode ( $res, true );
	if ($res ['errcode'] == 0) {
		return $res ['short_url'];
	} else {
		return $long_url;
	}
}



/**
 * php curl 请求链接
 * 当$post_data为空时使用GET方式发送
 * @param unknown $url
 * @param string $post_data
 * @return mixed
 */
function curlSend($url,$post_data=""){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	if($post_data != ""){
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}



/**

 * 调用接口获取 $ACCESS_TOKEN
 * 微信缓存 7200 秒，这里使用thinkphp的缓存方法
 * @param unknown $APP_ID
 * @param unknown $APP_SECRET
 * @return Ambigous <mixed, Thinkmixed, object>
 */
function get_accesstoken($APP_ID,$APP_SECRET){
	$ACCESS_TOKEN = S($APP_ID);
	if($ACCESS_TOKEN == false){
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APP_ID."&secret=".$APP_SECRET;
		$json = curlSend($url);
		$data=json_decode($json,true);
		S($APP_ID,$data[access_token],7000);
		$ACCESS_TOKEN = S($APP_ID);
	}

	return $ACCESS_TOKEN;
}

/**
 * 微信网页JSSDK  调用接口获取 $jsapi_ticket
 * 微信缓存 7200 秒，这里使用thinkphp的缓存方法
 * @param unknown $ACCESS_TOKEN
 * @return Ambigous <mixed, Thinkmixed, object>
 */
function get_jsapi_ticket($ACCESS_TOKEN){
	$jsapi_ticket = S($ACCESS_TOKEN);
	if($jsapi_ticket == false){
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$ACCESS_TOKEN."&type=jsapi";
		$json = curlSend($url);
		$data = json_decode($json,true);

		S($ACCESS_TOKEN,$data[ticket],7000);
		$jsapi_ticket = S($ACCESS_TOKEN);
	}

	return $jsapi_ticket;
}

/**
 * 微信网页JSSDK 获取签名字符串
 * 所有参数名均为小写字符
 * @param unknown $nonceStr 随机字符串
 * @param unknown $timestamp 时间戳
 * @param unknown $jsapi_ticket
 * @param unknown $url 调用JS接口页面的完整URL，不包含#及其后面部分
 */
function get_js_sdk($APP_ID,$APP_SECRET){
	$protocol = (!empty($_SERVER[HTTPS]) && $_SERVER[HTTPS] !== off || $_SERVER[SERVER_PORT] == 443) ? "https://" : "http://";
	$url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];

	$argu = array();
	$argu[appId] = $APP_ID;
	$argu[url] = $url;
	$argu[nonceStr] = createNonceStr();
	$argu[timestamp] = time();

	$ACCESS_TOKEN = get_accesstoken($APP_ID, $APP_SECRET);
	$argu[jsapi_ticket] = get_jsapi_ticket($ACCESS_TOKEN);

	$string = "jsapi_ticket=".$argu[jsapi_ticket]."&noncestr=".$argu[nonceStr]."&timestamp=".$argu[timestamp]."&url=".$argu[url];
	$argu[signature] = sha1(trim($string));
	return $argu;
}

/**
 * 获取随机字符串
 * @param number $length
 * @return string
 */
function createNonceStr($length = 16) {
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$str = "";
	for ($i = 0; $i < $length; $i++) {
		$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	}
	return $str;
}
/**
*导出Execl表格
*@param $expFileName  string  导出文件名称
*@param $expTitle  string  表格中页签的名称
*@param $expCellName  array  列名
*@param $expTableData  array  表格数据
*/
function exportExcel($expFileName,$expTitle,$expCellName,$expTableData){
    ob_clean();
    try{
        $fileName = iconv('UTF-8','GB2312',$expFileName);
        $xlsTitle = $expTitle;
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);

        import("Org.Util.PHPExcel");
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel=new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        import("Org.Util.PHPExcel.Reader.Excel5");

        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objPHPExcel->getActiveSheet(0)->setTitle($expTitle); //设置页签名称
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
        }

        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;charset=utf-8;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

        return true;
    }catch(Exception $e){
        return false;
    }
}

//获取不同类别的积分
function getIntegral($type="",$bankId){
		$res= M('customer_config')->where(array('bank_id'=>$bankId))->find();
		$zong_res =M('shop_config')->where(array('id'=>1))->find();
	if($type=="ZC"){		   //注册
		return $res['reg_integral']?$res['reg_integral']:$zong_res['reg_integral'];
		
	}elseif($type=="TJ"){		//推荐
		return $res['recom_integral']?$res['recom_integral']:$zong_res['recom_integral'];
		
	}elseif($type=="FX"){		//分享
		return $res['share_integral']?$res['share_integral']:$zong_res['share_integral'];
		
	}elseif($type=="GZ"){		//关注
		return $res['follow_integral']?$res['follow_integral']:$zong_res['follow_integral'];
		
	}elseif($type=="GM"){		//购买商品一元等于几个积分
		return $res['integral_rate']?$res['integral_rate']:$zong_res['integral_rate'];
		
	}elseif($type=="QD"){       //签到
		return $res['sign_integral']?$res['sign_integral']:$zong_res['sign_integral'];
		
	}elseif($type=="FXCS"){     //分享次数
		return $res['share_tims']?$res['share_tims']:$zong_res['share_tims'];
		
	}elseif($type=="KFQQ"){     //客服QQ
		return $res['servise_qq']?$res['servise_qq']:$zong_res['servise_qq'];
		
	}elseif($type=="KFDH"){     //客服电话
		return $res['servise_phone']?$res['servise_phone']:$zong_res['servise_phone'];
		
	}
	return "0";
	
}






/**
 * 判断是否关注
 */
function panduanguanzhu($member_id=0,$openid=0){
//	file_put_contents('./11101.txt',$member_id.'-----'.$openid);
	$token = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".C('APPID')."&secret=".C('AppSecret'));
	$token_json = json_decode($token,true);
//	file_put_contents('./110.txt',var_export($token_json,true));
	if($member_id){
		$openid = M('Member')->where("id=".$member_id)->getField('wx_openid');
		$json = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token_json['access_token']."&openid=".$openid."&lang=zh_CN");
		$isguanzhu = json_decode($json,true);
	}else{
		$json = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token_json['access_token']."&openid=".$openid."&lang=zh_CN");
		$isguanzhu = json_decode($json,true);
	}
	if(!is_numeric($isguanzhu['subscribe'])){
		$isguanzhu['subscribe'] = 1;
	}
	return $isguanzhu['subscribe'];
}
function remotepost($ary,$url){
	$postdata = http_build_query(
        $ary
    );
    $opts = array('http' =>
		array(
		  'method'  => 'POST',
		  'header'  => 'Content-type: application/json',
		  'content' => $postdata
		)
    );
    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
}
/*
	online_order
	online_order_no	String(64)	是	订单号 必须唯一
	online_order_client_name	String(50)	是	订货人名称
	online_order_phone	String(20)	是	联系电话
	online_order_address	String(250)	否	收货地址
	online_order_memo	String(250)	否	订单备注
	online_order_total_money	Double	是	订单总金额(元)
	online_order_payment_money	Double	是	买家实付金额(元)
	online_order_create_time	Date	是	下单时间
	online_order_payment_time	Date	否	付款时间
	online_order_details
	item_num	Integer	是	商品唯一码
	online_item_price	Double	是	基本单位单价
	online_item_qty	Double	是	基本单位数量
	online_item_discount_money	Double	是	折扣金额(元)
	online_item_total_money	Double	是	订货金额(元)  服务端会验证online_item_qty * online_item_price = online_item_total_money   明细的online_item_total_money之和必须等于online_order_total_money
	online_order_total_money - 明细中的online_item_discount_money之和     =   online_order_payment_money
	online_order_post_fee 运费
*/

function erpaddorder($orderlistid){
	//查询商品信息  查询门店在ERP系统里的编号
	$orderlist = M('Orderlist')->alias('a')->join("left join ys_store b on a.storeid=b.id")->field('a.id,a.userid,a.storeid,a.orderno,a.consignee,a.dealerid,a.province,a.city,a.district,a.address,a.telephone,a.addtime,a.total_price,a.shipping_fee,a.pay_fee,a.paytime,b.erpstoreid')->where("a.id=$orderlistid")->find();

	if($orderlist){
		//判断门店编号是否存在
		if(!$orderlist['erpstoreid']){
			return false;
		}
		//查询商品详细信息  查询ERP系统里的商品id
		$orderdetaillist = M('Orderdetail')->alias('a')->join("left join ys_goods b on a.goodsid=b.id")->field('a.goodsid,a.goodsname,a.goodsid,a.guige,a.num,a.good_nowprice,a.allprice,b.erpgoodsid2')->where("a.orderid=$orderlist[id]")->select();
		$orderDetailRecode = array();
		if(is_array($orderdetaillist)){
			if($orderlist['shipping_fee']>0){
				$orderlist['total_price'] -= $orderlist['shipping_fee'];
				$orderlist['pay_fee'] -= $orderlist['shipping_fee'];
				//等于一表示是非购物车购买，需要扣掉运费
				if(count($orderdetaillist)==1){
					$orderdetaillist[0]['allprice'] -= $orderlist['shipping_fee'];
				}
			}
			$goodsModel = M('Goods');
			$ceshiprice = 0;
			//将所有商品的详细信息和接口对应
			foreach($orderdetaillist as $key => $val){
				//查询商品编号
				//$online_order_fid = $goodsModel->where("id=".$val['goodsid'])->getField('erpgoodsid2');
				$online_order_fid = json_decode($val['erpgoodsid2'],true);
				foreach($online_order_fid as $bkey => $bval){
					if($bkey==$orderlist['dealerid']){
						$item_num = $bval;
						break;
					}
				}
				array_push($orderDetailRecode,
					array(
						'item_num'=>$item_num,'online_item_name'=>$val['goodsname'],'online_item_price'=>$val['good_nowprice'],'online_item_qty'=>$val['num'],'online_item_discount_money'=>0,'online_item_total_money'=>$val['allprice'],'online_item_spec'=>$val['guige']
					)
				);
			}
			//将订单信息和接口对应
			//TODO
			$orderRecode = json_encode(array(
				'online_order_no'=>$orderlist['orderno'],'online_order_client_name'=>""/*$orderlist['consignee']*/,'online_order_phone'=>""/*$orderlist['telephone']*/,'online_order_address'=>$orderlist['city'].$orderlist['district'].$orderlist['address'],'online_order_total_money'=>$orderlist['total_price'],'online_order_payment_money'=>$orderlist['pay_fee'],'online_order_create_time'=>$orderlist['addtime'],'online_order_payment_time'=>$orderlist['paytime'],'online_order_payment_type'=>'微信支付','online_order_payment_no'=>'********','online_order_post_fee'=>$orderlist['shipping_fee'],'online_order_details'=>$orderDetailRecode
			));

			$time = date('Y-m-d H:i:s');
			//获取appid和key
			$erpData = M('Dealer')->field('erpappid,erpkey')->where("id=".$orderlist['dealerid'])->find();
			$appid = $erpData['erpappid'];
			$key = $erpData['erpkey'];
			$sign = MD5($key.'app_id'.$appid.'branch_num'.$orderlist['erpstoreid'].'methodonline_order.uploadonline_order'.$orderRecode.'timestamp'.$time.$key);
	        $data = array(
				'app_id'=>$appid,
				'branch_num'=>$orderlist['erpstoreid'],//门店编号
				'method'=>'online_order.upload',//在线订单
				'online_order'=>$orderRecode,
				'timestamp'=>$time,
				'sign' => $sign
			);
			$result = remotepost($data,'http://api.nhsoft.cn/pos3API/entry/order');
			$result = json_decode($result,true);
			if($result['data']){
		    	$ewmtext=$result['data'];
				$filename=time()."$orderlist[id].png";
				$picPath="Uploads/Picture/QRcode/shop";
				$filename = qrcode($ewmtext,$filename,$picPath,false,5); //自动生成二维码
				M('Orderlist')->where("id=$orderlist[id]")->save(array('barcode'=>$filename,'online_order_fid'=>$result['data'],'erpstoreid'=>$orderlist['erpstoreid']));
			}
		}
	}
	return $result;
}



/*
 * 中文字数截取
 * @date: 2016-9-6下午17:32
 * @editor: bielang
 */


/**
 *   发送邮件验证码
 *   @email 收件人的邮箱地址
 *   @title 邮件主题
 *   @content 邮件内容
 */
function sendEmailCode($email,$title,$content){
    header('Content-Type:text/html;Charset=utf-8');
    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new \PHPMailer;
    $mail->isSMTP();                                      // 设置邮件使用SMTP
    $mail->Host = C('MAIL_HOST');                     // 邮件服务器地址
    $mail->SMTPAuth = C('MAIL_SMTPAUTH');                               // 启用SMTP身份验证
    $mail->CharSet = C('MAIL_CHARSET');                             // 设置邮件编码
    $mail->setLanguage('zh_cn');
    //ltcsoxuneycvdeii                      // 设置错误中文提示
    $mail->Username = C('MAIL_USERNAME');              // SMTP 用户名，即个人的邮箱地址
    $mail->Password = C('MAIL_PASSWORD');                        // SMTP 密码，即个人的邮箱密码
    $mail->SMTPSecure = 'tls';                            // 设置启用加密，注意：必须打开 php_openssl 模块
    $mail->Priority = 3;                                  // 设置邮件优先级 1：高, 3：正常（默认）, 5：低
    $mail->From = C('MAIL_FROM');                 // 发件人邮箱地址
    $mail->FromName = C('MAIL_FROMNAME');                     // 发件人名称
    $mail->Port = "25";

    $mail->addAddress($email);     // 添加接受者
    //$mail->addAddress('ellen@example.com');               // 添加多个接受者
    //$mail->addReplyTo('info@example.com', 'Information'); // 添加回复者
    //$mail->addCC('mail2@sina.com');                // 添加抄送人
    //$mail->addCC('mail3@qq.com');                     // 添加多个抄送人
    //$mail->ConfirmReadingTo = 'admin@elmhome.com.cn';     // 添加发送回执邮件地址，即当收件人打开邮件后，会询问是否发生回执
    //$mail->addBCC('mail4@qq.com');                    // 添加密送者，Mail Header不会显示密送者信息
    $mail->WordWrap = 50;                                 // 设置自动换行50个字符
    //$mail->addAttachment('./1.jpg');                      // 添加附件
    //$mail->addAttachment('/tmp/image.jpg', 'one pic');    // 添加多个附件
    $mail->isHTML(C('MAIL_ISHTML'));                                  // 设置邮件格式为HTML
    $mail->Subject = $title;
    $mail->Body    = "【洛凡金融】尊贵的会员，您的验证码：".$content;
    //$mail->AltBody = 'This is the 主体 in plain text for non-HTML mail clients';
    if(!$mail->send()) {
//        echo 'Message could not be sent.';
//        echo 'Mailer Error: ' . $mail->ErrorInfo;
//        exit;
        return false;
    }
    return true;
    //echo 'Message has been sent';
    /*Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new \PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD') ; //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($email,"尊敬的客户");
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
    $mail->Subject =$title; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
    return($mail->Send());*/
    /**
     * 注：本邮件类都是经过我测试成功了的，如果大家发送邮件的时候遇到了失败的问题，请从以下几点排查：
     * 1. 用户名和密码是否正确；
     * 2. 检查邮箱设置是否启用了smtp服务；
     * 3. 是否是php环境的问题导致；
     * 4. 将26行的$smtp->debug = false改为true，可以显示错误信息，然后可以复制报错信息到网上搜一下错误的原因；
     * 5. 如果还是不能解决，可以访问：http://www.daixiaorui.com/read/16.html#viewpl
     *    下面的评论中，可能有你要找的答案。
     */

    //Vendor('phpmail.email.class.php');
    // require './ThinkPHP/Library/Vendor/phpmail/email.class.php';
    // //******************** 配置信息 ********************************
    // $smtpserver = "smtp.163.com";//SMTP服务器
    // $smtpserverport =25;//SMTP服务器端口
    // $smtpusermail = "luckilyley@163.com";//SMTP服务器的用户邮箱
    // $smtpemailto = $email;//发送给谁
    // $smtpuser = "luckilyley@163.com";//SMTP服务器的用户帐号
    // $smtppass = "";//SMTP服务器的用户密码
    // $mailtitle = $title;//邮件主题
    // $mailcontent = "<h1>".$content."</h1>";//邮件内容
    // $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
    // //************************ 配置信息 ****************************
    // $smtp = new \smtp($smtpserver,$smtpserverport,false,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
    // $smtp->debug = true;//是否显示发送的调试信息
    // $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);
    // dump($state);
    // echo "<div style='width:300px; margin:36px auto;'>";
    // if($state==""){
    //  echo "对不起，邮件发送失败！请检查邮箱填写是否有误。";
    //  echo "<a href='index.html'>点此返回</a>";
    //  exit();
    // }
    // echo "恭喜！邮件发送成功！！";
    // echo "<a href='index.html'>点此返回</a>";
    // echo "</div>";
}

function sendEmail($email,$title="产品申请提醒",$content=""){
    header('Content-Type:text/html;Charset=utf-8');
    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new \PHPMailer;
    $mail->isSMTP();                                      // 设置邮件使用SMTP
    $mail->Host = C('MAIL_HOST');                     // 邮件服务器地址
    $mail->SMTPAuth = C('MAIL_SMTPAUTH');                               // 启用SMTP身份验证
    $mail->CharSet = C('MAIL_CHARSET');                             // 设置邮件编码
    $mail->setLanguage('zh_cn');
    //ltcsoxuneycvdeii                      // 设置错误中文提示
    $mail->Username = C('MAIL_USERNAME');              // SMTP 用户名，即个人的邮箱地址
    $mail->Password = C('MAIL_PASSWORD');                        // SMTP 密码，即个人的邮箱密码
    $mail->SMTPSecure = 'tls';                            // 设置启用加密，注意：必须打开 php_openssl 模块
    $mail->Priority = 3;                                  // 设置邮件优先级 1：高, 3：正常（默认）, 5：低
    $mail->From = C('MAIL_FROM');                 // 发件人邮箱地址
    $mail->FromName = C('MAIL_FROMNAME');                     // 发件人名称
    $mail->Port = "25";

    $mail->addAddress($email);     // 添加接受者
    //$mail->addAddress('ellen@example.com');               // 添加多个接受者
    //$mail->addReplyTo('info@example.com', 'Information'); // 添加回复者
    //$mail->addCC('mail2@sina.com');                // 添加抄送人
    //$mail->addCC('mail3@qq.com');                     // 添加多个抄送人
    //$mail->ConfirmReadingTo = 'admin@elmhome.com.cn';     // 添加发送回执邮件地址，即当收件人打开邮件后，会询问是否发生回执
    //$mail->addBCC('mail4@qq.com');                    // 添加密送者，Mail Header不会显示密送者信息
    $mail->WordWrap = 50;                                 // 设置自动换行50个字符
    //$mail->addAttachment('./1.jpg');                      // 添加附件
    //$mail->addAttachment('/tmp/image.jpg', 'one pic');    // 添加多个附件
    $mail->isHTML(C('MAIL_ISHTML'));                                  // 设置邮件格式为HTML
    $mail->Subject = $title;
    $mail->Body    = "【洛凡金融】尊贵的小贷公司，有客户已经申请您的产品".$content;
    //$mail->AltBody = 'This is the 主体 in plain text for non-HTML mail clients';
    if(!$mail->send()) {
//        echo 'Message could not be sent.';
//        echo 'Mailer Error: ' . $mail->ErrorInfo;
//        exit;
        return false;
    }
    return true;

}

/**
 * 功能：生成二维码
 * @param string $qr_data   手机扫描后要跳转的网址
 * @param string $qr_level  默认纠错比例 分为L、M、Q、H四个等级，H代表最高纠错能力
 * @param string $qr_size   二维码图大小，1－10可选，数字越大图片尺寸越大
 * @param string $save_path 图片存储路径
 * @param string $save_prefix 图片名称前缀
 */
function createQRcode($save_path,$qr_data='PHP QR Code :)',$qr_level='L',$qr_size=4,$save_prefix='qrcode'){
    if(!isset($save_path)) return '';
    //设置生成png图片的路径
    $PNG_TEMP_DIR = & $save_path;
    //导入二维码核心程序
    vendor('PHPQRcode.class#phpqrcode');  //注意这里的大小写哦，不然会出现找不到类，PHPQRcode是文件夹名字，class#phpqrcode就代表class.phpqrcode.php文件名
    //检测并创建生成文件夹
    if (!file_exists($PNG_TEMP_DIR)){
        mkdir($PNG_TEMP_DIR,'0777',true);
    }
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    if (isset($qr_level) && in_array($qr_level, array('L','M','Q','H'))){
        $errorCorrectionLevel = & $qr_level;
    }
    $matrixPointSize = 4;
    if (isset($qr_size)){
        $matrixPointSize = & min(max((int)$qr_size, 1), 10);
    }
    if (isset($qr_data)) {
        if (trim($qr_data) == ''){
            die('data cannot be empty!');
        }
        //生成文件名 文件路径+图片名字前缀+md5(名称)+.png
        $filename = $PNG_TEMP_DIR.$save_prefix.md5($qr_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        //开始生成
        QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    } else {
        //默认生成
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    }
    if(file_exists($PNG_TEMP_DIR.basename($filename)))
        return basename($filename);
    else
        return FALSE;
}


function searchApply($phone=""){
    $url="http://cps.ppdai.com/bd/GetUserListing";
	$data = "ChannelId=%s&SourceId=%s&token=%s&phone=%s&sign=%s";
	$ChannelId  =   212;
	$SourceId   =   372;
	$token      =   '293764805a1c4c6a9a2189f4e89fb52a'; 
    $phone      =   $phone;
    $string     =   'token='.$token.'&phone='.$phone;
    $paramMd5Str=   MD5($string);
    $sign       =   MD5($string.'&paramMd5='.$paramMd5Str );
	$rdata = sprintf($data, $ChannelId, $SourceId, $token, $phone,$sign);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	$result = curl_exec($ch);
	curl_close($ch);
    //$content    =   json_decode($result);

    return $result;
     
}

/**
 * @param $data
 * user_id 用户id
 * type 1-收入 2-支出
 * cate 1-买断
 * amount 货币数量
 * order_id 订单id
 * message 流水信息
 */
function moneyWater($data)
{
    $data['add_time'] = date('Y-m-d H:i:s',time());
    M("money_water")->add($data);
}

 ?>