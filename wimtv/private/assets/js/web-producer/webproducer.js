// TODO RIVEDERE il web-producer e controllare che funzioni su altri computer
// TODO provare a sloggarsi da peer e riprovare

LiveProducer ={
	startWebProducer: function(hostId, callback) {
		var swfVersionStr = "11.4.0";
		var xiSwfUrlStr = "private/assets/js/web-producer/playerProductInstall.swf";
		var flashvars = {};

		var params = {};
		params.quality = "high";
		params.bgcolor = "#ffffff";
		params.allowscriptaccess = "sameDomain";
		params.allowfullscreen = "true";

		var attributes = {};
		attributes.align = "left";

		swfobjectProd.embedSWF(
			"private/assets/js/web-producer/producer.swf", "producer",
			320 * 2, 240 * 1.5,
			swfVersionStr, xiSwfUrlStr,
			flashvars, params, attributes);

		swfobjectProd.createCSS("#producer", "display:block;text-align:center;");
		callback();
	},
	startProd: function(liveUser, liveUrl, livePwd) {
		// inserisco timeout perche' il DOM sia ready con SWF
		setTimeout(function() {
			var index = liveUrl.lastIndexOf('/');
			var producer = $('#producer')[0];
			console.log(producer);
			producer.setCredentials(liveUser, livePwd);
			producer.setUrl(liveUrl.substring(0, index));
			producer.setStreamName(liveUrl.substring(index + 1, liveUrl.length));
			producer.setAttribute("align", "center");
			producer.setAttribute('style', "margin: auto; " + producer.getAttribute('style'));
			producer.setStreamWidth(640);
			producer.setStreamHeight(360);
			producer.connect();
		}, 1500);
	}
};
