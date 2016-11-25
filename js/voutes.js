$(document).ready(function() {
  $('.article-icon').on('click', function() {
    var elem = $(this);
    var pubId = elem.parent().attr('id');
    var count = elem.next();
    var val = count.html();

    $.ajax({
      url: './voutes/'+pubId,
      type: 'POST',
      success: function(xhr, data, textStatus) {
        if (xhr === 'vouted') {
          elem.addClass('vouted');
          count.addClass('vouted');
          count.text(parseInt(val) + 1);
        } else if (xhr === 'unvouted') {
          elem.removeClass('vouted');
          count.removeClass('vouted');
          count.text(parseInt(val) - 1);
        }
      },
      error: function(xhr, textStatus, errorObj) {
        alert('Произошла критическая ошибка!');
      }
    });
  });
});
