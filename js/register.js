$(document).ready(function() {
  var name = $('#name');
  var nameError = $('#name-desc');
  var password = $('#password');
  var passwordError = $('#password-desc');
  var button = $('#button');

  name.on('keyup', function() {
    var value = $(this).val();
    $.ajax({
      url: './user',
      type: 'POST',
      data: {
        name: value
      },
      success: function(xhr, data, textStatus) {

        name.removeClass('sign-input-error');
        name.removeClass('sign-input-correct');
        nameError.html('');
        nameError.removeClass('sign-desc-active');
        nameError.removeClass('sign-desc-correct');
        nameError.removeClass('sign-desc-error');

        if (value.length === 0) {

          button.prop("disabled", true);

        } else if (xhr === 'no' && value.length >= 3) {

          name.addClass('sign-input-correct');

          nameError.addClass('sign-desc-active');
          nameError.addClass('sign-desc-correct');

          nameError.html('Correct');

          button.prop("disabled", false);

        } else if (xhr === 'yes' || value.length < 3) {

          name.addClass('sign-input-error');

          if (value.length < 3) {
            nameError.html('Name is too short');
          } else {
            nameError.html('User already exists');
          }

          nameError.addClass('sign-desc-active');
          nameError.addClass('sign-desc-error');

          button.prop("disabled", true);
        } else {
          console.log('error');
        }
      },
      error: function(xhr, textStatus, errorObj) {
        alert('Произошла критическая ошибка!');
      }
    });
  });
  password.on('keyup', function() {
    var value = $(this).val();

    password.removeClass('sign-input-error');
    password.removeClass('sign-input-correct');
    passwordError.html('');
    passwordError.removeClass('sign-desc-active');
    passwordError.removeClass('sign-desc-correct');
    passwordError.removeClass('sign-desc-error');

    if (value.length === 0) {

      button.prop("disabled", true);

    } else if (value.length >= 3) {

      password.addClass('sign-input-correct');
      passwordError.addClass('sign-desc-active');
      passwordError.addClass('sign-desc-correct');

      passwordError.html('Correct');

      button.prop("disabled", false);

    } else {

      password.addClass('sign-input-error');
      passwordError.addClass('sign-desc-active');
      passwordError.addClass('sign-desc-error');

      passwordError.html('Password is too short');

      button.prop("disabled", true);
    }
  });
});
