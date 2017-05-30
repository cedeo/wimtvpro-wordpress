var RestService = {};

RestService.remoteEndpoint = "https://new.wim.tv"
RestService.getWimtvServer = function() {
  return this.remoteEndpoint + "/wimtv-server"
};

RestService.handshakeToken = function(shakeCallback, credentials) {
  /* Handshake for PUBLIC authentication */
  userData = {
    'grant_type': 'client_credentials'
  };
  appAuth = "d3d3Og==";

  /* Handshake for PRIVATE authentication */
  if (credentials) {
    try {
      // Accesss Token Provided directly
      if (credentials.accessToken) {
        return shakeCallback("Bearer " + credentials.accessToken);
      }
      userData = {
       'username': credentials.username,
       'password': credentials.password,
       'grant_type': 'password'
      };
      appAuth = credentials.appId;
    } catch(e) {
      console.error('missing credentials');
    }
  }

  // getting public token
  $.ajax(this.getWimtvServer() + "/oauth/token", {
    method: 'POST',
    data: userData,
    headers: {
      "Authorization": "Basic " + appAuth
    }
  }).done(function (data, textStatus, jqXHR ) {
    shakeCallback("Bearer " + data.access_token);
  });
};

/**
 * Private. For Handling ALL Rest Call.
 * This still calls $http, and wrap promises into callback. Not so nice. We may just use promises (but we have to do a big refactoring)
 * @param path  URI
 * @param method
 * @param contentType
 * @param data
 * @param callbackSuccess
 * @param callbackFailure
 */
RestService.call = function (path, method, contentType, data, callbackSuccess, callbackFailure) {
  var callbackSuccess = callbackSuccess || function() { console.log("It was a success")};
  var callbackFailure = callbackFailure || function(res) {
    console.log("It was a failure", JSON.stringify(res.data.responseText));
  };

  var url = this.getWimtvServer() + path;
  var headers = {
    'Content-Type': contentType,
    'Accept': 'application/json',
    'Accept-Language': "en",
    'X-Wimtv-timezone': -(new Date().getTimezoneOffset() * 60 * 1000)
  };

  // Handshake Token and call API
  RestService.handshakeToken(function(authHeader){
    headers['Authorization'] = authHeader;
    // TODO "Bearer " + (context=="public" ? tokens.public : tokens.private);
    $.ajax({
      method: method,
      url: url,
      dataType: 'json',
      data: data,
      headers: headers
    })
    .done(function(res, textStatus, jqXHR) {
      if(jqXHR.status >= 200 && jqXHR.status < 300) {
        callbackSuccess({'data':res}, []);
      } else {
        callbackFailure({'data':res});
      }
    })
    .fail(function (res) {
      callbackFailure({'data': res});
    });
  }, data.auth);
  //var context = path.search(/public\/|oauth\//) > 0 ? "public" : "private";
};

/**
 * PUBLIC INTERFACES
 */

RestService.wimTvRestGET = function (path, callbackSuccess, callbackError) {
  RestService.call(path, 'GET', 'application/json', null, callbackSuccess, callbackError);
};

RestService.wimTvRestPOST = function (path, data, callbackSuccess, callbackError) {
  RestService.call(path, 'POST', 'application/json', data, callbackSuccess, callbackError);
};

RestService.wimTvRestDELETE = function (path, callbackSuccess, callbackError) {
  RestService.call(path, 'DELETE', 'application/json', null, callbackSuccess, callbackError);
};

RestService.wimTvRestCustom = function (path, method, contentType, data, callbackSuccess, callbackError) {
  RestService.call(path, method, contentType, data, callbackSuccess, callbackError);
};
