<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="gb2312">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>文件管理</title>
<style type="text/css">
*{font-size:12px;}
tr{height:25px;line-height:25px;background-color:#efefef;}
td{text-indent:5px;}
tr:hover{background-color:#666;color:#fff;}
tr:hover a{color:#fff;}
cite{float:right;margin-right:3px;font-style:normal;}
cite a{color:#fff;}
.trTitle{background-color:#555;font-weight:bold;color:#fff;}
.dir{background-color:#99FFCC;}
.file{background-color:#FFFFCC;}

h3{margin:0px;padding:0px;background-color:#555;color:#fff;text-indent:10px;height:25px;line-height:25px;}
h3 span{margin-left:10px;}

textarea{background-color:#f7f7f7;}

ul{list-style:none;margin:0px;padding:0px;height:50px;}
li{float:right;width:50px;height:50px;line-height:50px;text-align:center;margin-right:3px;background-color:#666;}
li a{color:#fff;text-decoration:none;display:block;width:50px;height:50px;font-weight:bold;}
li a:hover{background-color:#999;}
</style>
<script type="text/javascript">
function opendiv(action,divId)
{
	if(action=="open")
	{
		document.getElementById(divId).style.display="block";
	}
	else
	{
		document.getElementById(divId).style.display="none";
	}
}
function fillfile(urlstr,file)
{
	document.getElementById('movefileurl').value=urlstr;
	document.getElementById('movefilename').value=file;
	//document.getElementById('testdiv').innerHTML=urlstr+"|"+file;
	opendiv('open','movefileform');
}
</script>
</head>
<body>
<div style="width:600px;border:1px solid #666;">
<div style="width:100%;background-color:#555;height:50px;"><span style="float:left;font-weight:bold;font-size:14px;color:#fff;margin-left:10px;margin-top:5px;">FileSystem Admin :: PHP::FireFox</span>
  <ul>
    <li><a href="javascript:history.go(-1);">向上</a></li>
    <li><a href="javascript:opendiv('open','uploadform');">上传</a></li>
    <li><a href="javascript:opendiv('open','newdirform');">新目录</a></li>
    <li><a href="loaddir.php">根目录</a></li>
  </ul>
</div>
<?
//获取参数
$action=$_GET["action"];
$urlStr=$_GET["urlstr"];
$fileName=$_GET["filename"];
$file=$_GET["file"];
ini_set('date.timezone','Asia/Shanghai');

//上传表单
echo "<div style=\"display:none;width:600px;height:100px;border-bottom:1px solid #999;position:absolute;top:60px;background-color:#f7f7f7;\" id=\"uploadform\">";
echo "<h3><cite><a href=\"javascript:opendiv('close','uploadform');\">Close</a></cite><span>上传文件：</span></h3>";
echo "<div style=\"width:100%;height:75px;line-height:75px;text-align:center;\">";
echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"loaddir.php?action=upload&filename=$fileName\">请选择文件：<input type=\"file\" name=\"upload_file\" /> <input type=\"submit\" value=\"Upload\" /></form>";
echo "</div>";
echo "</div>";

//新建目录表单
echo "<div style=\"display:none;width:600px;height:100px;border-bottom:1px solid #999;position:absolute;top:60px;background-color:#f7f7f7;\" id=\"newdirform\">";
echo "<h3><cite><a href=\"javascript:opendiv('close','newdirform');\">Close</a></cite><span>新建目录：</span></h3>";
echo "<div style=\"width:100%;height:75px;line-height:75px;text-align:center;\">";
echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"loaddir.php?action=newdir&filename=$fileName\">文件夹名称：<input type=\"text\" name=\"dir_name\" style=\"width:150px;\" /> <input type=\"submit\" value=\"Add\" /></form>";
echo "</div>";
echo "</div>";

//移动文件表单
echo "<div style=\"display:none;width:600px;height:80px;border-bottom:1px solid #999;position:absolute;top:10px;background-color:#f7f7f7;\" id=\"movefileform\">";
echo "<h3><cite><a href=\"javascript:opendiv('close','movefileform');\">Close</a></cite><span>移动文件：</span></h3>";
echo "<div style=\"width:100%;height:55px;text-align:center;\">";
echo "<form method=\"post\" action=\"loaddir.php?action=move\"><p style='margin:0px;padding:0px;height:20px;line-height:20px;color:#ff0000;'>注：如果目标文件夹不存在，将会被创建。可以填目录里目录，如:test/test，结尾不能有/。</p>目标文件夹名称：<input type=\"text\" name=\"todirname\" style=\"width:150px;\" /> <input type='hidden' name='urlstr' value='' id='movefileurl' /> <input type='hidden' name='file' value='' id='movefilename' /> <input type=\"submit\" value=\"Move\" /></form>";
echo "<div id='testdiv'></div>";
echo "</div>";
echo "</div>";

//输出表格
echo "<table style=\"width:100%;\">";

//读文件夹下的文件 
function loadDir($dirName)
{
	if($handle=openDir($dirName))
	{
		echo "<tr class='trTitle'><td>类型</td><td>文件名</td><td>大小</td><td>最后修改时间</td><td>操作</td></tr>";
		while(false!==($files=readDir($handle)))
		{
			if($files!="."&&$files!="..")
			{
				$urlStrs=$dirName."/".$files;
				
				if(is_dir($dirName."/".$files))
				{
					$types="目录";
					$cons="<a href=\"loaddir.php?action=open&filename=$urlStrs\">Open</a> | <a href='loaddir.php?action=del&urlstr=$urlStrs'>Del</a>";
					$className="dir";
					$fileaTime="";
					$fileSize=getSize($dirName."/".$files);
					echo "<tr class='$className'><td>$types</td><td>$files</td><td>".$fileSize." bytes</td><td>$fileaTime</td><td>$cons</td></tr>";
				}
				else 
				{
					$types="文件";
					$cons="<a href=\"javascript:fillfile('$urlStrs','$files');\">Move</a> | <a href=\"loaddir.php?action=edit&urlstr=$urlStrs\">Edit</a> | <a href='loaddir.php?action=del&urlstr=$urlStrs'>Del</a>";
					$className="file";
					$fileSize=getSize($dirName."/".$files);
					$fileaTime=date("Y-m-d H:i:s",getEditTime($dirName."/".$files));
					$arrayfile[]="<tr class='$className'><td>$types</td><td>$files</td><td>".$fileSize." bytes</td><td>$fileaTime</td><td>$cons</td></tr>";
				}
				//echo "<tr class='$className'><td>$types</td><td>$files</td><td>".$fileSize." bytes</td><td>$fileaTime</td><td>$cons</td></tr>";
			}
		}
		//$arraydirLen=count($arraydir);
		//for($i=0;$i<$arraydirLen;$i++) echo $arraydir[$i];
		$arrayfileLen=count($arrayfile);
		for($i=0;$i<$arrayfileLen;$i++) echo $arrayfile[$i];
		//echo $arraydir;
		closeDir($handle);
	}
}

//删除文件
function killFile($filename)
{
	if(file_exists($filename))
	{
		if(!is_dir($filename))
		{
			unlink($filename);
			echo "成功删除文件:".$filename;
		}
		else 
		{
			rmDir($filename);
			echo "成功删除目录:".$filename;
		}
	}
	else 
	{
		echo "指定文件或文件夹不存在";
	}
	//echo "可不能想删就删啊";
}

//获取文件大小
function getSize($a)
{
	if(file_exists($a))
	{
		return fileSize($a);
	}
}

//获取最后修改时间
function getEditTime($a)
{
	if(file_exists($a))
	{
		return filemTime($a);
	}
}

//读取文件
function readFiles($a)
{
	//$exts=substr($a,-3);
	$exts=explode(".",$a);
	$extsNums=count($exts);
	$exts=$exts[$extsNums-1];
	if($exts=="php"||$exts=="asp"||$exts=="txt"||$exts=="html"||$exts=="aspx"||$exts=="jsp"||$exts=="htm")
	{
		$handle=@fOpen($a,"r");
		if($handle)
		{
			echo "<h3>修改文件：$a</h3>";
			echo "<form action='loaddir.php?action=doedit&urlstr=$a' method='post'><textarea style='width:99%;height:300px;margin-left:auto;margin-right:auto;' name='content'>";
			while(!fEof($handle))
			{
				//$buffer=fGets($handle);
				//echo ubb(mb_convert_variables(fGets($handle),"gb2312","gb2312,utf-8"));
				echo ubb(mb_convert_encoding(fGets($handle),"gb2312","utf-8,gb2312"));
				//echo ubb(iconv("utf-8,gb2312","gb2312",fGets($handle)));
				//echo ubb(fGets($handle));
			}
			fClose($handle);
			echo "</textarea><h3><input type='submit' value='Edit' /></h3></form>";
		}
		else 
		{
			echo "文件不存在或不可用";
		}
	}
	else 
	{
		echo "不能编辑该文件";
	}
}

//修改文件
function editFiles($a)
{
	$contents=get_magic_quotes_gpc()?stripslashes($_POST["content"]):$_POST["content"];
	//if(get_magic_quotes_gpc()) $contents=stripslashes($_POST["content"]); else $contents=$_POST["content"];
	
	if(is_writeable($a))
	{
		//echo substr(sprintf('%o',fileperms($a)),-4);
		if(!$handle=@fopen($a,"wb"))
		{
			echo "不能打开文件：$a";
			exit;
		}
		if(@fwrite($handle,$contents)===FALSE)
		{
			echo "不能写入到文件：$a";
		}
		else 
		{
			echo "修改文件成功";
		}
		@fclose($handle);
	}
	else 
	{
		echo "不能修改文件";
	}
	/*if(function_exists("file_put_contents"))
	{
		if(file_put_contents($a,$contents))
		{
			echo "修改文件成功";
		}
		else 
		{
			echo "修改文件失败";
		}
	}
	else 
	{
		echo "貌似不能使用修改功能";
	}*/
}

//字符转换
function ubb($str)
{
	$str=str_replace("<","&lt;",$str);
	$str=str_replace(">","&gt;",$str);
	return $str;
}

//上传处理
function upLoadFile($a)
{
	if(is_dir($a)||$a==""||$a==".")
	{
		if($_FILES)
		{
			$dest=$a?$a."/":$a;
			$file_name=$dest.$_FILES['upload_file']['name'];
			//echo $file_name;
			if(move_uploaded_file($_FILES['upload_file']['tmp_name'],$file_name))
			{
				echo "上传成功";
			}
			else 
			{
				echo "上传失败";
			}
		}
	}
	else 
	{
		echo "所在路径不是一个目录，不能上传";
	}
	//echo "可不能想传就传";
}

//新建目录
function newDir($a)
{
	if(is_dir($a)||$a==""||$a==".")
	{
		$dest=$a?$a."/":$a;
		$dirName=$_POST['dir_name'];
		if(mkdir($dest.$dirName))
		{
			echo "创建目录成功";
		}
		else 
		{
			echo "创建目录失败";
		}
	}
	else 
	{
		echo "所在路径不是一个目录，不能创建目录";
	}
	//echo "可不能想建就建";
}

//移动文件
function moveFile()
{
	$todirname=$_POST['todirname'];
	$fileurlstr=$_POST['urlstr'];
	$file=$_POST['file'];
	//echo $todirname."<br/>";
	//echo $fileurlstr."<br/>";
	//echo $file."<br/>";
	if(!is_dir($todirname))
	{
		mkdir($todirname);
	}
	if(!is_dir($fileurlstr))
	{
		if(rename($fileurlstr,$todirname."/".$file))
		{
			echo "移动文件成功";
		}
		else 
		{
			echo "移动文件失败";
		}
	}
	//echo "可不能想移就移";
}

//具体操作
if(!$action)
{
	loadDir(".");
}
elseIf($action=="del")
{
	killFile($urlStr);
}
elseIf ($action=="edit")
{
	readFiles($urlStr);
}
elseif ($action=="doedit")
{
	editFiles($urlStr);
}
elseif($action=="upload")
{
	upLoadFile($fileName);
}
elseif($action=="newdir")
{
	newDir($fileName);
}
elseif($action=="move")
{
	moveFile();
}
else
{
	loadDir($fileName);
}
?>
</table>
</div>
</body>
</html>