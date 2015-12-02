<!DOCTYPE html>
<!--
                                
 .-.                . .  .   .  
(  |-. .-. .-..-..-,| |/\|.-,|-.
 `-' '-`-`-' '' '`'-'-'  '`'-`-'

-->
<?
    if (!empty($_GET['view'])) {
        $ts = $_GET['view'];
        $path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'files' 
            . DIRECTORY_SEPARATOR . $ts . DIRECTORY_SEPARATOR;
        $files = glob($path . '*.ipa');
        if (!empty($files) && count($files) == 1) {
            $ipaInfo = pathinfo($files[0]);
            $ipa['file'] = $ipaInfo['basename'];
            $ipa['deleteUrl'] = 'delete.php?id=' . $ts;
            $ipa['created'] = date('j M Y G:i:s', $ts);
            $ipa['currentUrl'] = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') 
                . '://' . "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";;
            $ipa['qr'] = 'https://chart.googleapis.com/chart?chs=140x140&cht=qr&chld=H|1&chl=' 
                . $ipa['currentUrl'];

            require('php' . DIRECTORY_SEPARATOR . 'ipaTools.php');
            $targetPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'files' 
                    . DIRECTORY_SEPARATOR . $ts . DIRECTORY_SEPARATOR;
            $targetFile =  $targetPath . $ipa['file'];
            $ipaTools = new IpaTools($targetFile, true);
            $ipa['devices'] = $ipaTools->getDevices();
            $ipa['installUrl'] = $ipaTools->applink;
        } else {
            die('errorone');
        }
    }
?>

<html>
<head>
    <title>BEdita – IPA Ad Hoc Distribution</title>
    <meta charset="utf-8">
    <link href="css/dropzone.css" type="text/css" rel="stylesheet" />
    <link href="css/ios-dist.css" type="text/css" rel="stylesheet" />
    <script src="js/dropzone.min.js"></script>
</head>
<body class="<? if (!empty($_GET['view'])) { ?>view<? } else { ?>upload<? }?>">

<div id="main">
    <header>
        <h2>BEdita tools</h2>
        <h1>IPA Ad Hoc Test Distribution</h1>
    </header>

    <section>
        <article class="dropzone" <? if (empty($_GET['view'])) { ?> id="dropzone" <? } ?>>
            <a href="<? if (!empty($ipa['deleteUrl'])) { echo $ipa['deleteUrl']; } ?>" class="delete-btn">delete</a>
            <label><? if (!empty($ipa['file'])) { echo $ipa['file']; } ?></label>
            <p><? if (!empty($ipa['file'])) { echo 'created ' . $ipa['created']; } ?></p>
            <input type="text" value="<? if (!empty($ipa['currentUrl'])) { echo $ipa['currentUrl']; } ?>">
            <img src="<? if (!empty($ipa['qr'])) { echo $ipa['qr']; } ?>" class="qr">
        </article>
    

        <article id="ipa-url" class="hide"><h2>url</h2><a href="<? if (!empty($ipa['installUrl'])) echo $ipa['installUrl']; ?>">Install</a></article>
        <article id="ipa-devices" class="hide"><h2>UDID (device list)</h2><ul><?
            if(!empty($ipa['devices'])) {
                foreach ($ipa['devices'] as $udid) {
                    echo '<li>' . $udid . '</li>';
                }
            }
        ?></ul></article>
    </section>

    <footer>
        <img src="img/cwlogo.svg"><a href="http://www.channelweb.it" target="_blank">2015 ChannelWeb</a>
    </footer>
</div>

<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js'></script>
<script type="text/javascript" src="js/ios-dist.js"></script>

</body>
</html>
