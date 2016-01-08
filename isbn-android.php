<?php

//Configuration en fonction du SIGB :
$aleph_base = 'http://bib.univ-lr.fr/client/bulr/search/detailnonmodal/ent:$002f$002fSD_ILS$002f156$002fSD_ILS:156155/one?qu=';
$aleph_search_isbn_params = '&te=ILS';
$current_url = 'https://survey.univ-lr.fr/isbn-android.php';
//

//User agent
$ua = $_SERVER['HTTP_USER_AGENT'];

switch (true) {
    case preg_match('/android/i',$ua): 
        $app_href='market://details?id=com.google.zxing.client.android';
        $app_img='//zxing.appspot.com/img/badge.png';
        break;
    case (preg_match('/iphone/i',$ua)||preg_match('/ipad/i',$ua)):
        $app_href='itms-apps://itunes.apple.com/app/id416098700';
        $app_img='//devimages.apple.com.edgekey.net/app-store/marketing/guidelines/images/badge-download-on-the-app-store.svg';
        break;
    default:
        $app_href='market://details?id=com.google.zxing.client.android';
        $app_img='//zxing.appspot.com/img/badge.png';
}



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
       <br><a href="<?php echo $app_href;?>"><img src="<?php echo $app_img;?>"></a>
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


    //$html = curl($search_url);
    if (preg_match("!<title>PDS SSO</title>!", $data)) {
        if (preg_match("!url = '(.*)'!", $data, $m)) {        
            $data = curl($m[1]);
        } else {
            echo "error, redirect url not found\n";
            exit(0);
        }
    }
    if (preg_match("!<title>Login </title>!", $data)) {
        if (preg_match("!body onload = \"location = '/goto/(.*)'!", $data, $m)) {
            $data = curl($m[1]);
        }
    }
    if (preg_match("!http?://.*&set_number=\d+!", $data, $m)) {
        return $m[0];
    } else {
        return $search_url;
    }
*/
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
