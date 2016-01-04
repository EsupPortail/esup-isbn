<?php

//Configuration en fonction du SIGB :
$aleph_base = 'http://sushi-new.univ-paris1.fr';
$aleph_search_isbn_params = '/F/?pds_handle=GUEST&func=find-d&local_base=UPS01&find_code=ISBN&request=';
//
$current_url = 'http://area51.univ-paris1.fr/prigaux/isbn.php';
//

if ($_GET["code"]) {
    header("Location: " . search_aleph($_GET["code"]));    
} else {
    $dest = "zxing://scan/?ret=" . urlencode($current_url . "?code={CODE}");
?>
<html>
  <meta name="viewport" content="width=device-width">
    <a href="<? echo $dest ?>">
       <img src="//zxing.appspot.com/img/app.png">
       <br>scan barcode
   </a>
   <p>
   <br>To scan code with your mobile camera you need to install free Barcode Scanner -app
   <br><a href="market://details?id=com.google.zxing.client.android"><img src="//zxing.appspot.com/img/badge.png"></a>
</html>
<?php
}

function search_aleph($code) {
    $search_url = $GLOBALS['aleph_base'] . $GLOBALS['aleph_search_isbn_params'] . $code;

    $html = curl($search_url);

    if (preg_match("!<title>PDS SSO</title>!", $html)) {
        if (preg_match("!url = '(.*)'!", $html, $m)) {        
            $html = curl($m[1]);
        } else {
            echo "error, redirect url not found\n";
            exit(0);
        }
    }
    if (preg_match("!<title>Login </title>!", $html)) {
        if (preg_match("!body onload = \"location = '/goto/(.*)'!", $html, $m)) {
            $html = curl($m[1]);
        }
    }
    if (preg_match("!http?://.*&set_number=\d+!", $html, $m)) {
        return $m[0];
    } else {
        return $search_url;
    }
}

function curl($url) {
  //echo "getting $url\n";
  $ch=curl_init($url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $output=curl_exec($ch);
  curl_close($ch);
  return $output;
}
?>
