<?php

//Configuration en fonction du SIGB :
$sirsi_base = 'http://bib.univ-lr.fr/client/bulr/search/results?qu=';
$sirsi_search_isbn_params = '';
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
	header("Location: " . search_bib($_GET["code"]));
} else {
    $dest = "zxing://scan/?ret=" . urlencode($current_url . "?code={CODE}");
    ?>
    <html>
       <form action="isbn-android.php" method="get">
          <meta name="viewport" content="width=device-width">
          <a href="<? echo $dest ?>">
          <img src="//zxing.appspot.com/img/app.png">
          <br>Scanner le code-barre du livre
          </a>
          <br><br>Ou entrer le code ISBN :<input type=text name="code" size="20" maxlength="100">
          <button type="submit">Disponible ?</button>
          <p>
          <br>
          <br>Pour scanner le code-barre du livre avec l'appareil photo de votre téléphone, vous devrez peut-être installer une application supplémentaire.
          <br><a href="<?php echo $app_href;?>"><img src="<?php echo $app_img;?>"></a>
       </form>
    </html>
    <?php
}

function search_bib($code) {
    $search_url = $GLOBALS['sirsi_base'] . $code; // . $GLOBALS['sirsi_search_isbn_params'];

    $timeout=5;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $search_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    curl_setopt($curl, CURLOPT_NOPROXY, '*');
    // ... set others params and options ...
    $data= curl_exec($curl);
    curl_close($curl);
    
    return $search_url;
}
?>
