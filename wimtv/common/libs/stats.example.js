$(document).ready(function() {
  var username = window.location.search.substring(1).split("=")[1]
  || 'raffaella.guccione';
  var vsaStats = new VsaStats(username,'http://localhost:3000/proxy');

  var populateResult = function(data) {
    $('.results').empty();
    Object.keys(data).forEach(function(index) {
      var res = '<p>' + index + ': ' +
          JSON.stringify(data[index], null, 2) + '</p><br/>'
      $('.results').append(res);
    });
  };

  $('#infoOnUser').on('click', function() {
    vsaStats.infoOnUser(
      populateResult,
      $('.startDate').val(),
      $('.endDate').val()
    );
  });

  $('#infoOnStreams').on('click', function() {
    vsaStats.infoOnStreams(
      populateResult,
      $('.startDate').val(),
      $('.endDate').val()
    );
  });

  $('#infoOnStreamDetailed').on('click', function() {
    vsaStats.infoOnStreamDetailed(
      populateResult,
      $('.startDate').val(),
      $('.endDate').val(),
      $('.streamId').val()
    );
  });
});
