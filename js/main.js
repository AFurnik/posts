$(document).ready(function() {
  var accountLink = $('#account'),
    menu = $('#menu');
    
  accountLink.on('click', function() {
    accountLink.toggleClass('active');
    menu.toggleClass('active');
  });

});
