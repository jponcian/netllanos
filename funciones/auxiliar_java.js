function pregunta_eliminar() {
  if (confirm("�Estas seguro Eliminar la Informacion?")) {
    return true;
  } else {
    return false;
  }
}

function pregunta_guardar() {
  if (confirm("�Estas seguro de Guardar la Informacion?")) {
    return true;
  } else {
    return false;
  }
}

function pregunta_anular() {
  if (confirm("�Estas seguro de Anular la Informacion?")) {
    return true;
  } else {
    return false;
  }
}

//Incluir esto en el imput onkeypress="return SoloMoneda(event,this)"
function SoloMoneda(e, field) {
  key = e.keyCode ? e.keyCode : e.which;
  // backspace
  if (key == 8) return true;

  // 0-9 a partir del .decimal
  if (field.value != "") {
    if (field.value.indexOf(".") > 0) {
      //si tiene un punto valida dos digitos en la parte decimal
      if (key > 47 && key < 58) {
        if (field.value == "") return true;
        //regexp = /[0-9]{1,10}[\.][0-9]{1,3}$/
        regexp = /[0-9]{2}$/;
        return !regexp.test(field.value);
      }
    }
  }
  // 0-9
  if (key > 47 && key < 58) {
    if (field.value == "") return true;
    regexp = /[0-9]{9}/;
    return !regexp.test(field.value);
  }
  // .
  if (key == 46) {
    if (field.value == "") return false;
    regexp = /^[0-9]+$/;
    return regexp.test(field.value);
  }
  // other key
  return false;
}

function SoloRif(e) {
  //recibe el evento de teclado
  key = e.keyCode || e.which;
  //Variable teclado
  teclado = String.fromCharCode(key).toLowerCase();
  //variable letras
  var test = document.getElementById("ORIF").value;
  if (test.length < 1) {
    letras = "vgej";
  } else {
    letras = "1234567890";
  }
  //caracteres especial
  especiales = "8-37-38-46";

  teclado_especial = false;

  for (var i in especiales) {
    if (key == especiales[i]) {
      teclado_especial = true;
      break;
    }
  }

  if (letras.indexOf(teclado) == -1 && !teclado_especial) {
    return false;
  }
}

function marcar(obj, x) {
  if (obj.checked) {
    document.getElementById("fila" + x).style.backgroundColor = "lightblue";
  } else document.getElementById("fila" + x).style.backgroundColor = "";
}
