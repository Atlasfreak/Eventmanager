//Toggle pw visibility
function togglePw() {
  var pw = document.getElementById('pass-input');
  document.getElementById('toggle-btn').classList.toggle('lock-open');
  if (pw.type === "password") {
    pw.type = "text";
  } else {
    pw.type = "password";
  }
}
window.onload = function () {
  //get all Inputs
  var inputs = document.querySelectorAll('input,select')
  let input_types = ["text", "number", "tel", "email"]

  for (var i = 0; i < inputs.length; i += 1) {
    //ignore radiobuttons and passwords
    if (inputs[i].id === "captcha") continue;
    if (input_types.includes(inputs[i].type)) {
      //restore input value
      inputs[i].value = sessionStorage.getItem(inputs[i].getAttribute('name'));
    }
    else if (inputs[i].type === "select-one") {
      if (sessionStorage.getItem(inputs[i].getAttribute('name')) === null) {
        inputs[i].value = "";
      }
      else {
        inputs[i].value = sessionStorage.getItem(inputs[i].getAttribute('name'));
      }
    }
  }
  //detect change in form and add value to sessionStorage
  $('form :input').change(function () {
    sessionStorage.setItem(this.getAttribute('name'), this.value);
  });
}

function numeric(field) {
  if (!(field.value == parseInt(field.value))) {
    console.log('not numeric');
    field.classList.add('err-shadow');
  }
  else {
    field.classList.remove('err-shadow');
  }
}
