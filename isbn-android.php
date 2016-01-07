<?php

//Configuration en fonction du SIGB :
$aleph_base = 'http://bib.univ-lr.fr/client/bulr/search/detailnonmodal/ent:$002f$002fSD_ILS$002f156$002fSD_ILS:156155/one?qu=';
$aleph_search_isbn_params = '&te=ILS';
$current_url = 'https://survey.univ-lr.fr/test.php';
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
       <br>Scanner le code-barre du livre
   </a>
   <p>
   <br>Pour scanner le code-barre du livre avec l'appareil photo de votre téléphone, vous devrez peut-être installer une application supplémentaire.
   <br><a href="market://details?id=com.google.zxing.client.android"><img src="//zxing.appspot.com/img/badge.png"></a>
</html>
<?php
}

function search_aleph($code) {
    $search_url = $GLOBALS['aleph_base'] . $code . $GLOBALS['aleph_search_isbn_params'];

	$timeout=5;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $search_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_NOPROXY, '*');
	// ... set others params and options ...
	$data= curl_exec($curl);
	
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
