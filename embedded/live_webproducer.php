<?php
define('WP_USE_THEMES', false);
global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
include("../../../../wp-load.php");

$current_user = wp_get_current_user();

header('Content-type: text/html');
if ( !$current_user->exists() ) {

	echo "Non abilitato alla pagina";	

}
else {
  wp_enqueue_script('swfObject', plugins_url('script/swfObject/swfobject.js', dirname(__FILE__)));
  $id =  $_GET['id'];
	update_option('wp_liveNow', $id);

	$response = apiGetProfile();
	$dati = json_decode($response, true);
	$passwordLive = $dati['liveStreamPwd'];

    $embedded = apiEmbeddedLive($id);
	$arrayjson_live = json_decode($embedded);
	$url =  $arrayjson_live->url;
	$title = $arrayjson_live->name;
	
	$stream_url = explode ("/",$url);
	$stream_name = $stream_url[count($stream_url)-1];
	$url = "";
	for ($i=1;$i<count($stream_url)-1;$i++){
		$url .= $stream_url[$i] . "/";
	}
	$url = $stream_url[0] . "/" . $url;
	
	$url = substr($url, 0, -1);
?>

  
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title>Producer Live <?php echo $title;?></title>
	<?php
	wp_head();
	?>
	
	<style>
	div.pageproducer 	{
		width: 640px;
		margin: 10px auto;
	}

	</style>
	
</head>
<body style="text-align:center;"> 
<div id="page">	<h1>Producer</h1>
  
<p><?php _e("On this page you can view the video you're broadcasting live. Keep it open during the whole transmission.","wimtvpro");?></p>
<div  class="pageproducer">
<div id="producer" ></div>
</div>


<script type="text/javascript">
    function initSwf() {
        var url_pathPlugin ="<?php echo plugin_dir_url(dirname(__FILE__));?>";
        var xiSwfUrlStr = url_pathPlugin  + "script/swfObject/playerProductInstall.swf";
        console.log(xiSwfUrlStr );
        var flashvars = {};
        var params = {};
        params.quality = "high";
        params.bgcolor = "#ffffff";
        params.allowscriptaccess = "sameDomain";
        params.allowfullscreen = "true";
        var attributes = {};
        attributes.align = "left";

        swfobject.embedSWF(url_pathPlugin  + "script/swfObject/producer.swf", "producer", "640", "480", "11.4.0",xiSwfUrlStr, flashvars, params, attributes );
        setTimeout(function () {
            producer = jQuery('#producer')[0];
            console.log(producer);

            producer.setCredentials('<?php echo get_option("wp_userWimtv"); ?>', '<?php echo $passwordLive; ?>');
            producer.setUrl(decodeURIComponent('<?php echo $url;?>'));
            producer.setStreamName('<?php echo $stream_name;?>');
            producer.setStreamWidth(640);
            producer.setStreamHeight(480);
            producer.connect();
        }, 1000);

    }
    initSwf();
</script>


</div>

</body>

<?php
}

?>