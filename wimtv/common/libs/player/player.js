var Player = function (configOrSelector, config, instanciatedCallback, deferPlayerLoad) {
  var config = config || {};
  var mediaItem = "theplayer";
  var deferPlayerLoad = deferPlayerLoad || false;
  var self = this;

  // handle configuration
  if (configOrSelector) {
    if (typeof(configOrSelector) === 'string') {
      mediaItem = configOrSelector;
    }
    else if (typeof(configOrSelector) === 'object') {
      config = configOrSelector;
      mediaItem = config.mediaItemId || mediaItem
    }
  }
  // fallback for media item selector
  this.mediaItemId = (mediaItem.indexOf('#')<0 ? '#':'') + mediaItem;

  var errorCount = 0;
  function onError(event, player, error) {
    if(error.code === 4) {
      // reinit player
		setTimeout(function () {
			errorCount++;
			console.log("Re init: " + errorCount);
			document.getElementById("player-container").innerHTML = '<div id="theplayer" class="fp-edgy"></div>';
			flowplayer("#theplayer", player.conf);
		}, errorCount*1000);
    }
    console.log("onError");
  }

  /** Player Config **/
  this.playerConfig = {
    aspectRatio: 	'16:9',
    autoplay: true,
    fullscreen: true,
    native_fullscreen: true,
    live: false,
    key: '$324027419529824',
    facebook: true,
    twitter: true,
    // poster vs splash not set
    // TODO
    //swf: "../common/libs/player/flowplayer/flowplayer.swf",
    //swfHls: "../common/libs/player/flowplayer/flowplayerhls.swf",
    //embed: -> https://flowplayer.org/docs/sharing.html#embed-options
    clip: {},
  fullscreen: true,
  // ratio: 9/16,
  };
  this.config(config);

  /** Event listeners **/
  this.eventListeners = {};
  // add finish callback
  this.addEventListener('finish', function (e, api) {
    // change config
    // all players go to splash state on finish
    api.unload();
  });

  this.api;
  this.root;
  if (!deferPlayerLoad) {
    flowplayer(function (api, root) {
      self.api = api;
      self.root = root;

      // finalize setup of logo
      if (config.logoConfig && root.querySelector(".fp-logo")) {
        var logo = root.querySelector(".fp-logo");
        // customize logo link
        logo.href = config.logoConfig.link;
	    logo.target = '_blank';
        logo.style.display = config.logoConfig.hide? 'none':'';
      }

      self.api.on("error", onError);

      if (instanciatedCallback) instanciatedCallback(self);
    });
    // install flowplayer into selected container
    flowplayer(this.mediaItemId, this.playerConfig);
  }
  /* player */
  return this;
}

/**
 * Dynamic change player configuration
 *
 * cfr. https://flowplayer.org/docs/api.html#flowplayerset
 */
Player.prototype.config = function(config) {
  var player = this;
  Object.keys(config).forEach(function(configItem){
    if (player.api) {
      player.api.conf[configItem] = config[configItem];
    } else {
      player.playerConfig[configItem] = config[configItem];
    }
  })
  return this;
}

/**
 * Add event listener to available on player
 */
Player.prototype.addEventListener = function (eventName, callback) {
  if (this.api) { // player instanciated
    this.api.on(eventName,callback);
  }
  else {
    if (!this.eventListeners.hasOwnProperty(eventName)) {
      this.eventListeners[eventName] = [];
    }
    this.eventListeners[eventName].push(callback);
  }
  return this;
};

/**
 * Add control button to current skin
 */
Player.prototype.addControlButton = function (selector, callback,
  controlSelector) {
  var controlSelector = controlSelector || ".fp-controls";
  if (this.api) {  // player instanciated
    var btn = this.root.querySelector(selector);
    if (callback) { btn.onclick = callback }
    this.root.querySelector(controlSelector).appendChild(btn);
    return this;
  }
  throw "Player not instanciated";
};

// dynamically load a new source video
/*
srcs = [{
  type: "video/mp4",
  src: src
}]
*/
Player.prototype.dynamicLoad = function(srcs, callback) {
  this.api.load({sources: srcs}, callback);
  return this;
};

Player.prototype.start = function(sources) {
  var self = this;
  var eventListeners = this.eventListeners

  // player instanciated
  if (this.api) {
    Object.keys(eventListeners).forEach(function(listener){
      eventListeners[listener].forEach(function(itemCallback){
        self.api.on(listener,itemCallback);
      });
    });
    this.api.load({sources: sources});
    return this;
  }

  // Sources
  this.playerConfig.clip['sources'] = sources;
  flowplayer(function (api, root) {
    self.api = api;
    self.root = root;

    Object.keys(eventListeners).forEach(function(listener){
      eventListeners[listener].forEach(function(itemCallback){
        api.on(listener,itemCallback);
      });
    });
  });
  // install flowplayer into selected container
  flowplayer(this.mediaItemId, this.playerConfig);
}

/**
 * Calls native function on player
 */
Player.prototype.callNativeFn = function(fnName) {
  var args = Array.prototype.slice.call(arguments, 1);
  if (this.api.hasOwnProperty(fnName)) {
    this.api[fnName].apply(this.api, args);
  }
}

Player.prototype.addCuepoint = function() {
  // add argument
  [].unshift.call(arguments, "addCuepoint");
  this.callNativeFn.apply(this, arguments);
  return this;
}

Player.prototype.setCuepoints = function() {
  // add argument
  [].unshift.call(arguments, "setCuepoints");
  this.callNativeFn.apply(this, arguments);
  return this;
}

Player.prototype.play = function() {
  this.callNativeFn.apply(this, ["play"]);
  return this;
}

Player.prototype.pause = function() {
  this.callNativeFn.apply(this, ["pause"]);
  return this;
}

Player.prototype.stop = function() {
  this.callNativeFn.apply(this, ["stop"]);
  return this;
}

/**********************/
/**** Generic Utils ***/
/**********************/

Player.prototype.addContextMenu = function(contextItem) {
  var contextItem = contextItem || '<a href="http://new.wim.tv">Powered by WimTV</a>';
  if (typeof $ == 'undefined') {
    throw "JQuery not inizialized";
  }
  $('<div class="fp-context-menu fp-menu"></div>')
    .append(contextItem)
    .appendTo(this.mediaItemId);
};
Player.prototype.minimalizeSkin = function() {
  if (typeof $ == 'undefined') {
    throw "JQuery not inizialized";
  }
  $(this.mediaItemId).addClass('fp-slim');
};
