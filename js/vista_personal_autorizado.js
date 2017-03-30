
function click_authorized_stuf(id)
{
  var i_name = document.getElementById('vpa_i_name');
  var i_surname = document.getElementById('vpa_i_surname');
  var i_dni = document.getElementById('vpa_i_dni');
  var i_old_dni = document.getElementById('vpa_i_old_dni');

  var clicked_row = document.getElementById(id);

  var name = clicked_row.children.item(0);
  var surname = clicked_row.children.item(1);
  var dni = clicked_row.children.item(2);

  i_name.value = name.outerText;  
  i_surname.value = surname.outerText;  
  i_dni.value = dni.outerText;  
  i_old_dni.value = dni.outerText;  
}

function clean_stuf_data()
{
  var i_name = document.getElementById('vpa_i_name');
  var i_surname = document.getElementById('vpa_i_surname');
  var i_dni = document.getElementById('vpa_i_dni');
  var i_old_dni = document.getElementById('vpa_i_old_dni');

  i_name.value = '';  
  i_surname.value = '';  
  i_dni.value = '';  
  i_old_dni.value = '';  
}

