<?php
//Configuration en fonction de l'etablissement :
#$url_opac = 'http://bib.univ-lr.fr/client/bulr/search/detailnonmodal/ent:$002f$002fSD_ILS$002f156$002fSD_ILS:156155/one?qu=<code_ouvrage>&te=ILS';
//$url_opac = 'http://bib.univ-lr.fr/client/fr_FR/bulr/search/results?qu=<code_ouvrage>';
$url_opac = 'http://195.221.187.151/search/o?SEARCH=<code_ouvrage>';
#$url_scanisbn = 'https://me.univ-lr.fr/static/isbn-android.php';
$url_scanisbn = 'https://me.univ-lr.fr/static/isbn-android.php';

$search_by_ppn = 1;
//
$url_proxy = 'http://wwwcache.univ-lr.fr:3128';

//web service Sudoc ISBN to PPN :
$ws_isbntoppn = 'http://www.sudoc.fr/services/isbn2ppn/<code_isbn>&format=text/json';

$json = '{"localisation":[
           {"BU":"nom BU1","",0},
           {"BU":"nom BU2","http://195.221.187.151/search/o?SEARCH=179015044",1},
           {"BU":"nom BU3","http://195.221.187.151/search/o?SEARCH=179015044",1},
           {"BU":"nom BU4","",0},
           {"BU":"nom BU5","",0},
          ]
          }';



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


if ($_GET["cod_isbn"]) {
    $cod_isbn=$_GET["cod_isbn"];

    //$cod_ppn= search_ppn($cod_isbn);
    
    header("Location: ./isbn-localisation.php?ISBN=" . $cod_isbn);
    //} else {
        //echo "ouvrage introuvable !";
    //}
} else {
    $dest = "zxing://scan/?ret=" . urlencode($url_scanisbn . "?cod_isbn={CODE}");
    ?>
    <html>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       <form action="isbn-android.php" method="get">
          <meta name="viewport" content="width=device-width">
          <h2><center><? echo htmlentities('Disponibilité d\'un ouvrage en BU') ;?></center></h2>
          <a href="<? echo $dest ?>">
          <img src="//zxing.appspot.com/img/app.png" style="width:40%;max-width:150px;">
          <br>Scanner le code-barre du livre
          </a>
          <br><br>Ou
          <br>entrer le code ISBN :<br><input type=text name="cod_isbn" size="20" maxlength="100">
          <button type="submit">Disponible ?</button>
          <p>
          <br>
          <br>Pour scanner le code-barre du livre avec l'appareil photo de votre téléphone, vous devrez peut-être installer une application supplémentaire.
          <br><center><a href="<?php echo $app_href;?>"><img src="<?php echo $app_img;?>"></a></center>
       </form>
    </html>
    <?php
}


//function search_ppn($code) {
//
    //$search_ppn=str_replace("<code_isbn>",$code,$GLOBALS['ws_isbntoppn']);
//
    //$timeout=5;
    //$curl = curl_init();
    //curl_setopt($curl, CURLOPT_URL, $search_ppn);
    //curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    //// ... set others params and options ...
//
    //#pour les eventuels pbs de proxy :
    //curl_setopt($curl, CURLOPT_PROXY, $GLOBALS['url_proxy']);
    ////curl_setopt($curl, CURLOPT_PROXY, $url_proxy);
//
    //#$result = curl_exec($curl);
    //$decod_result = json_decode(curl_exec($curl),true);
//
    //curl_close($curl);
//
    //$ppn=$decod_result['sudoc']['query']['result']['ppn'];
    //if (!$ppn){
        //$ppn=$decod_result['sudoc']['query']['result'][0]['ppn'];
    //}
//
    //return $ppn;
//}
?>
