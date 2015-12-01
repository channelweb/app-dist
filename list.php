<!DOCTYPE html>
<!--
                                
 .-.                . .  .   .  
(  |-. .-. .-..-..-,| |/\|.-,|-.
 `-' '-`-`-' '' '`'-'-'  '`'-`-'

-->
<?
    $base = dirname($_SERVER['REQUEST_URI']);
    $path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
    $ipaList = glob($path . '*', GLOB_ONLYDIR);
?>

<html>
<head>
    <title>BEdita – IPA Ad Hoc Distribution</title>
    <meta charset="utf-8">
    <link href="css/ios-dist.css" type="text/css" rel="stylesheet" />
</head>
<bod class="ipa-list">

<div id="main">
    <header>
        <h2>BEdita tools</h2>
        <h1>IPA Ad Hoc Test Distribution</h1>
    </header>

    <section>
    <ul><?
            if(!empty($ipaList)) {
                foreach ($ipaList as $ipa) {
                    $dir = pathinfo($ipa, PATHINFO_BASENAME);
                    $url = $base . '?view=' . $dir;

                    echo '<li><a href="' . $url . '">' . pathinfo($ipa, PATHINFO_BASENAME);
                    echo ' – ' . date('j M Y G:i:s', $dir);

                    $ipaPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
                    $files = glob($ipaPath . '*.ipa');
                    if (!empty($files) && count($files) == 1) {
                        $ipaInfo = pathinfo($files[0]);
                        echo ' – ' . $ipaInfo['basename'];
                    }

                    echo '</a></li>';
                }
            }
        ?></ul>
    </section>

    <footer>
        <img src="img/cwlogo.svg"><a href="http://www.channelweb.it" target="_blank">2015 | ChannelWeb</a>
    </footer>
</div>

<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js'></script>
<script type="text/javascript" src="js/ios-dist.js"></script>

</body>
</html>
