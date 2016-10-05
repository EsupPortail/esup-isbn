<?php
//Configuration en fonction de l'etablissement :
$url_scanisbn = 'https://your-domain/isbn-scan.php';


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
    header("Location: ./isbn-localisation.php?cod_isbn=" . $cod_isbn);
} else {
    $dest = "zxing://scan/?ret=" . urlencode($url_scanisbn . "?cod_isbn={CODE}");
    ?>
    <html>
       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
       <form action="isbn-localisation.php" method="get">
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

?>
