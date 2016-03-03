<?php

//Configuration en fonction de l'etablissement :
# url permettant de connaitre la disponibilité de l'ouvrage (a recuperer sur le sudoc)
# sous la forme : http://catalogue.insa-rouen.fr/cgi-bin/koha/opac-search.pl?idx=kw&q=PPN<code_ppn>
$url_opac = '<code_ppn>';
$url_scanisbn = 'https://Survey.univ-etab.fr/scan-isbn.php';
//

//web service Sudoc ISBN to PPN :
$ws_isbntoppn = 'http://www.sudoc.fr/services/isbn2ppn/<code_isbn>&format=text/json';
//proxy :
$url_proxy = 'http://wwwcache.univ-lr.fr:3128';

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

    $ppn= search_ppn($code);
    $search_url=str_replace("<code_ppn>",$ppn,$GLOBALS['url_opac']);
#
    $timeout=5;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $search_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    // ... set others params and options ...

    #pour les eventuels pbs de proxy :
    curl_setopt($curl, CURLOPT_PROXY, $url_proxy);

    $data = curl_exec($curl);
    curl_close($curl);

    return $search_url;
}

function search_ppn($code) {
    $search_ppn=str_replace("<code_isbn>",$code,$GLOBALS['ws_isbntoppn']);

    $timeout=5;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $search_ppn);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    // ... set others params and options ...

    #pour les eventuels pbs de proxy :
    curl_setopt($curl, CURLOPT_PROXY, $url_proxy);

    #$result = curl_exec($curl);
    $decod_result = json_decode(curl_exec($curl),true);

    curl_close($curl);

    $ppn=$decod_result['sudoc']['query']['result']['ppn'];
    if (!$ppn){
        $ppn=$decod_result['sudoc']['query']['result'][0]['ppn'];
    }

    return $ppn;
}
?>
