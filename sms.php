<?php

error_reporting(0);
date_default_timezone_set("Asia/Jakarta");

  require 'class_curl.php';

  $curl = new curl();
  $curl->cookies('cookies/sms-'.md5($_SERVER['REMOTE_ADDR']).'.txt');
  $curl->ssl(0, 2);
  $curl->timeout(10);

if(!$_POST['checkbox'] && $_POST['kirim'] == 1){
  echo "<script type=\"text/javascript\">alert(\"MAKE SURE YOU'VE CLICK THE CHECKBOX\");</script>";
  echo '<script>setTimeout(function(){ window.location.href = "javascript:history.go(-1)"; }, 1);</script>';
}elseif($_POST['kirim'] == 1){

  //--# Header #--//
  $curl->header(
    array(
          'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
          'Referer: http://sms.payuterus.biz/alpha/',
          'Origin: http://sms.payuterus.biz/',
          'X-Requested-With: com.luckynineapps.smsgratis',
          'User-Agent: Mozilla/5.0 (Linux; Android 4.4.4; SM-G316HU Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/33.0.0.0 Mobile Safari/537.36',
          'Content-Type: application/x-www-form-urlencoded',
        )
  );

  // <span>4 + 4 = </span>
  $penjumlahan = getStr($curl->get('http://sms.payuterus.biz/alpha/'),'<span>',' = </span>');
  $menghitung = explode(" + ",$penjumlahan);
  $menjumlah = $menghitung[0]+$menghitung[1];

  $page = $curl->post(
    'http://sms.payuterus.biz/alpha/send.php',
    'nohp='.$_POST['nohp'].'&pesan='.$_POST['pesan'].' - dikirim oleh '.$_POST['pengirim'].'&captcha='.$menjumlah
  );

    if(strpos($page,'SMS Gratis Telah Dikirim')){
      $fp = fopen("history-sms.html", "a");
      fputs($fp,
        'Pengirim: '.$_POST['pengirim'].'<br>No HP: '.$_POST['nohp'].'<br>Pesan: '.$_POST['pesan'].'<br>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br>'
      );
      fclose($fp);
      echo "SMS successfully sent to <b>".$_POST['nohp']."</b><br><br>";
      echo '<a href="sms.php"><font color="blue">back</font></a><br/>';
      echo '<img src="http://moziru.com/images/cracker-clipart-animation-7.gif" height="500"><br>';
    }elseif(strpos($page,'Mohon Tunggu 8 Menit Lagi Untuk Pengiriman Pesan Yang Sama')){
      echo "Please Wait Another 8 Minutes For Sending the Same Message<br><br>";
      echo '<a href="sms.php"><font color="blue">back</font></a>';
    }elseif(!strpos($page,'SMS Gratis Telah Dikirim')){
      echo '<img src="http://approotz.com/wp-content/themes/approot/images/preloader.gif" height="200"><br>';
      echo '<meta http-equiv="refresh" content="2">';
    }else{
      echo $page;
    }
}else{
  // NEXT WITH CONFIRM
	echo
	'
	<form action="sms.php" method="post">
    <input type="hidden" name="kirim" value="1"/>
    Nama Pengirim: <input type="text" name="pengirim" size="15" placeholder="Suparman" maxlength="15" required /><br>
		No HP: <input type="tel" name="nohp" size="15" placeholder="081234567890" required /><br>
    Pesan:<br><textarea name="pesan" rows="8" cols="80"></textarea><br><br>
    Please click the checkbox <input type="checkbox" name="checkbox" value="1">
		<input type="submit" value="Submit">
	</form>

  <p>
    <i>Tested on <b>TSEL,TRI,AXIS,SMARTFREN</b> not working on <b style="color:red">IM3</b> - Thanks to SMS Gratis Indonesia</i><br />
    to form an API it would be nice to get permission first, I would love to hear that<br />
  	Click on the submit button, and the input will be sent to a page on the server called "/sms.php".
  </p>';
}
