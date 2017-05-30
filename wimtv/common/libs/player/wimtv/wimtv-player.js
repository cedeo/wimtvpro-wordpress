WimtvPlayer = {};

WimtvPlayer.remoteEndpoint = "https://new.wim.tv";
WimtvPlayer._play = function(data, idElement, player) {
  var streamUrl = data.uniqueStreamer;
  var sources = [];
  //if (data.result == "MESSAGE" || data.result == "BAND_LIMIT_EXCEEDED" || data.result == "PAYMENT_REQUEST") {
  if (data.result != "PLAY") {
    alert(data.message);
    return;
  }

  // config player and logo
  var playerLogo = WimtvPlayer.generateLogo(data);
  var thumb = WimtvPlayer.generateThumbnail(data);
  var config = {
    autoplay: (undefined !== data.autoplay? data.autoplay : true),
    // poster: thumb, // TODO check
    logoConfig: playerLogo,
    embed: false,
    logo: playerLogo.url,
    title: (data.title ? data.title : ''),
    live: (data.resource.channel || data.resource.channelId ? true : false)
  };
  // splash screen setup for autoplay is start
  if (config.autoplay === false) config.splash = thumb;

  // sources config: HLS + FLash Fallback
  data.srcs.forEach(function(src) {
    var stream = src.uniqueStreamer;
    if (stream.indexOf("rtmp://") >= 0) { // rtmp src
      config['rtmp'] = src.streamer.replace("&","%26");
      stream = src.file;
    }
    sources.push({
      type: src.mimeType,
      src: stream
    });
  });

  // fallback rtsp
  if (streamUrl.indexOf("rtsp://") != -1 &&
    navigator.userAgent.toLowerCase().match(/android/)) {
    // just open new location
    window.location = streamer;
  }

  config.clip = sources;
  player = player || new Player(idElement, config);
  /* Alternative start */
  /*
    // configure
    player.config(config);
    // start playback
    player.start(sources);
  */
  return {
    player: player,
    config: config,
    sources: sources};
};

WimtvPlayer._execPlay = function (apiCaller, url, data, configData, resolve, reject) {
  var data = data || {};
  /* Private Calls, Auth data are inserted into data json with key auth */
  if(undefined !== configData.isPublicCall && configData.isPublicCall == false) {
    data.auth = {
      username: configData.username,
      password: configData.password,
      accessToken: configData.accessToken,
      appId: configData.appId
    };
  }

  data = JSON.stringify(data);

  apiCaller(url, data, function success(response) { // success
    if(response.data.result === "PAYMENT_REQUIRED" || response.data.result === "SUBSCRIPTION_REQUIRED") {
      reject(response);
    }
    else {
      setTimeout(function () {
        var data = response.data, resource = data.resource
        var thumbnailId = null, authorCode = null;
        if (resource.eventId) {
          // is a Live Event
          thumbnailId = resource.channel.thumbnailId? resource.channel.thumbnailId : resource.channel.publisher.thumbnailId;
          authorCode = resource.channel.publisher.userCode;
        } else if (resource.publisher) {
          thumbnailId = resource.publisher.thumbnailId;
          authorCode = resource.publisher.userCode;
        }
        // eventually play
        var playRes = WimtvPlayer._play({
          srcs: data.srcs,
          file: data.file,
          resource: resource,
          uniqueStreamer: data.uniqueStreamer,
          streamer: data.streamer,
          hideLogo: configData.hideLogo,
          autoplay: WimtvPlayer.getAutoplay(configData.autoplay, (url.search(/preview\//) < 0)), // false for preview
          result: data.result || "PLAY", // PLAY or MESSAGE or BAND_LIMIT_EXCEEDED or PAYMENT_REQUEST. Play as default
          message: data.message || "" // empty in case of PLAY or PAYMENT_REQUEST, required for BAND_LIMIT_EXCEEDED or MESSAGE
        }, configData.playerId, configData.player);

        if(resolve) {
          resolve(response, playRes.player, playRes.config, playRes.sources);
        }
      }, 250);
    }
  }, // end success
  reject);
};

/**
  * Handles the playing of ANY element in wimtv.
  * @param resolve
  * @param reject
  *
  * @param configData = {
  *    eventType: <"vod"|"box"|"live">,
  *    eventId: <id evento>,
  *    isPublicCall: <true|false>,
  *    trackingId: <trackingId>
  *  }
  * @throw PlayerSetupError
  */
WimtvPlayer.play = function(configData, apiCaller, resolve, reject) {
  var configData = configData || {};
  var data = {}, eventType = configData.eventType;
  var publicCall = (undefined !== configData.isPublicCall) ? configData.isPublicCall : true;
  var url = "/api/" + (publicCall ? "public/" : "");
  this.checkParams(configData);

  switch (eventType) {
    case "vod":
    case "box":
      url += eventType + "/" + configData.eventId + "/play";
      break;
    case "live":
    case "cast":
      url += eventType + "/channel/" + configData.eventId + "/play";
      configData.live = true;
      break;
    default:
      reject();
  }

  if(configData.trackingId) {  // get trackingId
    data.trackingId = configData.trackingId;
  }
  this._execPlay(apiCaller, url, data, configData, resolve, reject);
};

/**
  * Handles the preview of ANY element in wimtv. Live are not previewable.
  * @param configData = {
  *    eventType: <"vod"|"box"|"live">,
  *    eventId: <id evento>,
  *    isPublicCall: <true|false>,
  *    trackingId: <trackingId>
  *  }
  * @param resolve
  * @param reject
  */
WimtvPlayer.preview = function(configData, apiCaller, resolve, reject) {
  var url = ["/api/",configData.eventType,"/",configData.eventId,"/play"].join('');
  this.checkParams(configData);
  this._execPlay(apiCaller, url, {}, configData, resolve, reject);
};

WimtvPlayer.vodPreview = function(configData, apiCaller, resolve, reject) {
  var url = ["/api/public/",configData.eventType,"/",configData.eventId,"/preview"].join('');
  this.checkParams(configData);
  this._execPlay(apiCaller, url, null, configData, resolve, reject);
};

WimtvPlayer.checkParams = function(configData) {
  if (!configData.hasOwnProperty('eventType') || "" == configData.eventType
    || !configData.hasOwnProperty('eventId') || "" == configData.eventId) {
    throw "PlayerSetupError eventType AND eventId must be set"
  }
};

/**
// TODO
  * Handles Payment for ANY element in wimtv.
  * @param eventType
  * @param itemId
  * @param options
  * @param resolve
  * @param reject
  */
WimtvPlayer.pay = function(apiCaller, eventType, itemId, options, resolve, reject) {
  var url;
  switch (eventType) {
    case "vod":
      url = "/api/public/" + eventType + "/" + itemId + "/pay";
      break;
    case "live":
      url = '/api/public/' + eventType + '/event/' + itemId + '/pay';
      break;
    case "license":
      url = '/api/license/' + itemId + '/subscribe';
      break;
    case "contentbundle":
      url = '/api/contentbundle/' + itemId + '/subscribe';
      break;
    case "marketplace":
      url = '/api/marketplace/' + itemId + '/pay';
      break;
    default:
      reject();	// todo: handle
  }

  // default options
  var opt = {
    returnUrl : "http://www.wim.tv/live/event/play",
    cancelUrl : "http://www.wim.tv/live/event/rejected",
    embedded : false,
    mobile : false
  };
  // overriding default options
  for(var k in options) {
    if(options.hasOwnProperty(k)) {
      opt[k] = options[k];
    }
  }
  apiCaller(
    url,
    opt,
    function (response) {
      // Move trackingId saving in resolver
      //config.setTrackingId(response.data.trackingId); // save the trackingId
      // payments: delegated to invoker OR redirect to paypal
      resolve? resolve(response) : (window.location = response.data.url);
    },
    function (response) {
      reject(response);
  });
};

/**
 * Generic thumbnail generator. Valid both for smaller (webtv logo) and for bigger ones (image)
 * @param id
 * @returns {string}
 */
WimtvPlayer.getThumbnail = function(id) {
  return this.remoteEndpoint + "/wimtv-server/asset/thumbnail/" + id;
};

WimtvPlayer.generateThumbnail = function(data) {
  if(undefined === data.resource) {
    return;
  }
  var thumb, fallbackThumb = "/public/assets/img/thumbnail.png";
  var res = data.resource;
  // video thumbnail
  if (res.thumbnailId) { // try with video thumbnail
    thumb = res.thumbnailId;
  }
  else if (res.channel) { // is a live.
    if (res.channel.thumbnailId) { // Try with channel thumbnail
      thumb =res.channel.thumbnailId;
    }
    else if (res.channel.publisher && res.channel.publisher.thumbnailId) { // Try with publisher thumbnail
      thumb = res.channel.publisher.thumbnailId;
    }
  }
  // data.thumbnailPath
  return thumb? WimtvPlayer.getThumbnail(thumb) : fallbackThumb;
};

WimtvPlayer.generateLogo = function(data) {
  // Default Logo
  var res = data.resource;
  var url, link;

  // publisher logo/thumbnail
  if(res) {
    if(res.channel) { // is a live
      // Link
      if(res.channel.publisher && res.channel.publisher.userCode) {
        link = "//wim.tv/#/webtv/" + res.channel.publisher.userCode;
      }
      // logo
      if(res.channel.thumbnailId) { // has channel Thumbnail
        url = res.channel.thumbnailId;
      }
      else if(res.channel.publisher && res.channel.publisher.thumbnailId) { // has publisher logo
        url = res.channel.publisher.thumbnailId;
      }
    }
    else if (res.publisher && res.publisher.thumbnailId) {
      url = res.publisher.thumbnailId;
      link = WimtvPlayer.remoteEndpoint + "/#/webtv/" + res.publisher.userCode
    }
  }
  var logo = {
    url: url ? WimtvPlayer.getThumbnail(url) : '/public/assets/img/thumbnail.png',
    link: link || '//wim.tv',
    // setup for Logo configuration "hide" option
    hide: data.hideLogo
  };
  return logo;
};

WimtvPlayer.getAutoplay = function(autoplay,isPreview) {
  if (undefined !== autoplay) {
    return autoplay;
  }
  return isPreview;
};
