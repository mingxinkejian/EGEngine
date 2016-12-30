<?php
// 为方便起见，使用autoload自动加载
$REQUIRE_PATH = __DIR__.DIRECTORY_SEPARATOR.'../../../../';

require $REQUIRE_PATH.'EGEngine'.'/'.'requireEGEngine.php';

use Extension\DataFrame\EGDelimiterBasedFrameDecoder;

$recData = "1234567890\r";
//模拟收到数据，并拆包
$decoder = new EGDelimiterBasedFrameDecoder(8096,"\r");
$i = 1;
while (1) {
	sleep(1);
	
	$ret = $decoder->input($recData);
	if ($ret == 0) {
		$recData .= "1234567890\rabc";
	}else if ($ret == -1) {
		break;
	}else {
		$pack = $decoder->decode($recData);
		//拆包成功输出包
		echo 'pack ' .$i . " :" . $pack.'  packLen:'.strlen($pack).PHP_EOL;
		$recData = substr($recData,$ret);
		$i++;
	}
}
//这种方法缺点也很明显，包体中如果含有相应解析的分隔符时就会出现拆包错误