$(document).ready(function() {
  var name = $('#name');
  var nameError = $('#name-desc');
  var password = $('#password');
  var passwordError = $('#password-desc');
  var button = $('#button');

  button.on('click', function(event) {
    event.preventDefault();
    var nameVal = name.val();
    var passwordVal = password.val();
    $.ajax({
      url: './signin',
      type: 'POST',
      data: {
        name: nameVal,
        password: passwordVal
      },
      success: function(xhr, data, textStatus) {
        console.log(xhr);
        if (xhr === 'ok') {
          window.location.replace("./");
        } else if (xhr === 'incorrectName') {
          name.addClass('sign-input-error');
          nameError.addClass('sign-desc-active');
          nameError.addClass('sign-desc-error');
          nameError.html('Incorrect name');
        } else if (xhr === 'incorrectPassword') {
          passwordError.html('Incorrect password');
          password.addClass('sign-input-error');
          passwordError.addClass('sign-desc-active');
          passwordError.addClass('sign-desc-error');
        }
      },
      error: function(xhr, textStatus, errorObj) {
        alert('Произошла критическая ошибка!');
      }
    });


  });
  name.on('keyup', function() {
    name.removeClass('sign-input-error');
    nameError.html('');
    nameError.removeClass('sign-desc-active');
    nameError.removeClass('sign-desc-error');
  });
  password.on('keyup', function() {
    password.removeClass('sign-input-error');
    passwordError.html('');
    passwordError.removeClass('sign-desc-active');
    passwordError.removeClass('sign-desc-error');
  });
});
