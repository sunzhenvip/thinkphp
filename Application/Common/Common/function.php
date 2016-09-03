<?php
/**
 * 为一个定单生成去支付宝支付的按钮
 *
 * @param unknown_type $orderId
 */
function buildAlipayBtn($orderId)
{
	// 把定单ID放到$_GET里因为alipay.php文件中是从$_GET里获取的
	$_GET['id'] = $orderId;
	// 生成按钮的变量
	include('./alipay/alipayapi.php');
	return $html_text;
}
function addStarOnEmail($email)
{
	$email = explode('@', $email);
	$firstChar = mb_substr($email[0], 0, 3, 'utf-8');
	$len = mb_strlen($firstChar, 'utf-8');
	if($len < 3)
		$firstChar = str_pad($firstChar, 3-$len, rand(1,9), STR_PAD_RIGHT);
	return str_pad($firstChar, 8, '*', STR_PAD_RIGHT).'@'.$email[1];
}
function sendMail($to, $title, $content)
{
	require_once('./PHPMailer_v5.1/class.phpmailer.php');
    $mail = new PHPMailer();
    // 设置为要发邮件
    $mail->IsSMTP();
    // 是否允许发送HTML代码做为邮件的内容
    $mail->IsHTML(TRUE);
    $mail->CharSet='UTF-8';
    // 是否需要身份验证
    $mail->SMTPAuth=TRUE;
    /*  邮件服务器上的账号是什么 -> 到163注册一个账号即可 */
    $mail->From=C('MAIL_ADDRESS');
    $mail->FromName=C('MAIL_FROM');    
    $mail->Host=C('MAIL_SMTP');            // 服务器IP
    $mail->Username=C('MAIL_LOGINNAME');  // 账号
    $mail->Password=C('MAIL_PASSWORD');   // 密码
    // 发邮件端口号默认25
    $mail->Port = 25;
    // 收件人
    $mail->AddAddress($to);
    // 邮件标题
    $mail->Subject=$title;
    // 邮件内容
    $mail->Body=$content;
    return($mail->Send());
}
/**
 * 清空一个目录以及子目录下所有的文件
 *
 * @param unknown_type $dirName : 要删除的目录，注：必须以/结尾
 */
function delFile($dirName, $canDeleteFileName='')
{
	$fp = opendir($dirName);
	while ($file = readdir($fp)) 
	{
		if($file == '.' || $file == '..')
			continue ;
		if($canDeleteFileName && $file == $canDeleteFileName)
			continue;
		if(is_dir($dirName. $file))
		{
			delFile($dirName. $file.'/');
			rmdir($dirName . $file);
		}
		else 
			unlink($dirName. $file);
	}
	closedir($fp);
}
/**
 * 用一个表中的数据构造下拉框
 *
 * @param unknown_type $modelName ： 模型名即表名
 * @param unknown_type $selectName ： <select name="xxx">这个下拉框标签的name
 * @param unknown_type $textFieldName ： <option value="">$v[xxx]</option> 表中用来当作文本输出的字段名称
 * @param unknown_type $valueFieldName ：<option value="$v[xxx]"></option>表中用来当作值提交的字段名称，默认是id
 * @param unknown_type $currentValue：    当前什么值的option默认为选中的状态
 */
function buildSelect($modelName, $selectName, $textFieldName, $valueFieldName='id', $currentValue='', $extraAttr='')
{
	$model = M($modelName);
	$data = $model->select();
	$html = '<select '.$extraAttr.' name="'.$selectName.'"><option value="">请选择</option>';
	foreach ($data as $v)
	{
		if($v[$valueFieldName] == $currentValue)
			$select = 'selected="selected"';
		else 
			$select = '';
		$html .= '<option '.$select.' value="'.$v[$valueFieldName].'">'.$v[$textFieldName].'</option>';
	}
	$html .= '</select>';
	echo $html;
}
function hasImage($name)
{
	foreach ($_FILES[$name]['name'] as $k => $v)
	{
		if(!empty($v))
			return TRUE;
	}
	return FALSE;
}
// 使用htmlpurifier包过滤数据
function removeXSS($string)
{
	require_once './HtmlPurifier/HTMLPurifier.auto.php';
	// 生成配置对象
	$_clean_xss_config = HTMLPurifier_Config::createDefault();
	// 以下就是配置：
	$_clean_xss_config->set('Core.Encoding', 'UTF-8');
	// 设置允许使用的HTML标签
	$_clean_xss_config->set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]');
	// 设置允许出现的CSS样式属性
	$_clean_xss_config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
	// 设置a标签上是否允许使用target="_blank"
	$_clean_xss_config->set('HTML.TargetBlank', TRUE);
	// 使用配置生成过滤用的对象
	$_clean_xss_obj = new HTMLPurifier($_clean_xss_config);
	// 过滤字符串
	return $_clean_xss_obj->purify($string);
}
/**
 * 删除一个数组中所有的图片
 *
 * @param unknown_type $image
 */
function deleteImage($image = array())
{
	$savePath = C('IMAGE_SAVE_PATH');
	foreach ($image as $v)
	{
		unlink($savePath . $v);
	}
}
/**
 * 上传图片并生成缩略图
 * 用法：
 * $ret = uploadOne('logo', 'Goods', array(
			array(600, 600),
			array(300, 300),
			array(100, 100),
		));
	返回值：
	if($ret['ok'] == 1)
		{
			$ret['images'][0];   // 原图地址
			$ret['images'][1];   // 第一个缩略图地址
			$ret['images'][2];   // 第二个缩略图地址
			$ret['images'][3];   // 第三个缩略图地址
		}
		else 
		{
			$this->error = $ret['error'];
			return FALSE;
		}
 *
 */
function uploadOne($imgName, $dirName, $thumb = array())
{
	// 上传LOGO
	if(isset($_FILES[$imgName]) && $_FILES[$imgName]['error'] == 0)
	{
		$rootPath = C('IMAGE_SAVE_PATH');
		$upload = new \Think\Upload(array(
			'rootPath' => $rootPath,
		));// 实例化上传类
		$upload->maxSize = (int)C('IMG_maxSize') * 1024 * 1024;// 设置附件上传大小
		$upload->exts = C('IMG_exts');// 设置附件上传类型
		/// $upload->rootPath = $rootPath; // 设置附件上传根目录
		$upload->savePath = $dirName . '/'; // 图片二级目录的名称
		// 上传文件 
		// 上传时指定一个要上传的图片的名称，否则会把表单中所有的图片都处理，之后再想其他图片时就再找不到图片了
		$info   =   $upload->upload(array($imgName=>$_FILES[$imgName]));
		if(!$info)
		{
			return array(
				'ok' => 0,
				'error' => $upload->getError(),
			);
		}
		else
		{
			$ret['ok'] = 1;
		    $ret['images'][0] = $logoName = $info[$imgName]['savepath'] . $info[$imgName]['savename'];
		    // 判断是否生成缩略图
		    if($thumb)
		    {
		    	$image = new \Think\Image();
		    	// 循环生成缩略图
		    	foreach ($thumb as $k => $v)
		    	{
		    		$ret['images'][$k+1] = $info[$imgName]['savepath'] . 'thumb_'.$k.'_' .$info[$imgName]['savename'];
		    		// 打开要处理的图片
				    $image->open($rootPath.$logoName);
				    $image->thumb($v[0], $v[1])->save($rootPath.$ret['images'][$k+1]);
		    	}
		    }
		    return $ret;
		}
	}
}
function showImage($image, $width='', $height='', $isReturn = FALSE)
{
	// 无论showImage调用多少次，C函数只调用了一次
	static $prefix = null;
	if($prefix === null)
		$prefix = C('IMAGE_PREFIX');
	if($width)
		$width = " width='$width'";
	if($height)
		$height = " height='$height'";
		
	if(!$image)
	{
		$str =  "<img $width $height src='/Public/Home/images/default_goods.jpg' />";
		if($isReturn)
			return $str;
		else 
			echo $str;
		return ;
	}
	
	$str =  "<img $width $height src='{$prefix}{$image}' />";
	if($isReturn)
		return $str;
	else 
		echo $str;
}