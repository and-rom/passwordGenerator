  function highlight(str) {
    str = str.replace(/\*/g, "<span class=\"highlight\">").replace(/_/g, "</span>");
    return str
  }

  function handleClick(){
    form = document.form;
    inputs = form.getElementsByTagName('input');
    values = {};
    string = "";
    for (i = 0; i < inputs.length; ++i) {
      switch (inputs[i].getAttribute("type")) {
        case "number":
          max_value = inputs[i].getAttribute("max");
          min_value = inputs[i].getAttribute("min");
          if (inputs[i].value > max_value) {
            inputs[i].value = max_value;
            string += "&";
            string += inputs[i].getAttribute("name") + "=" + inputs[i].value;
          } else if (inputs[i].value < min_value) {
            inputs[i].value = min_value;
            string += "&";
            string += inputs[i].getAttribute("name") + "=" + inputs[i].value;
          } else {
            string += "&";
            string += inputs[i].getAttribute("name") + "=" + inputs[i].value;
          }
          break;
        case "checkbox":
          string += "&";
          string += inputs[i].getAttribute("name") + "=" + (inputs[i].checked ? "1" : "0");
          break
        default:
          continue;
      }
    }
    console.log("passwordGenerator.php?format=json"+string);
    xmlhttp.open("GET","passwordGenerator.php?format=json&"+string,true);
    xmlhttp.send();
  }



var xmlhttp;

if (window.XMLHttpRequest) {
  // code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
} else {
  // code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function() {
  if (xmlhttp.readyState==4 && xmlhttp.status==200) {
    var data = this.responseText;
    try {
      var json_obj = JSON.parse(data);
      console.log("It's JSON");

      var table =  document.getElementById('passwords-table');
      if (typeof(table) != 'undefined' && table != null) {
        table.parentNode.removeChild(table);
      } else {
        document.getElementById("passwords").innerHTML = "";
      }

      var table = document.createElement('table');
      table.setAttribute('class', 'passwords');
      table.setAttribute('border', '1');
      table.setAttribute('id', 'passwords-table');      

      var table_body = document.createElement('tbody');

      for(var i=1;i<+json_obj.length;i++) {
        var tr = document.createElement('tr');
        tr.setAttribute('class', 'bg_' + ((i % 2) + 1));
        var td0 = document.createElement('td');
        var b = document.createElement('strong');
        b.innerHTML = (i < 10 ? "0" + i : i);
        td0.appendChild(b);

        var td1 = document.createElement('td');
        td1.innerHTML = json_obj[i]["password"];

        var td2 = document.createElement('td');
        td2.innerHTML = highlight(json_obj[i-1]["sentence"]);

        tr.appendChild(td0);
        tr.appendChild(td1);
        tr.appendChild(td2);
        table_body.appendChild(tr);
      }
      table.appendChild(table_body);

      document.getElementById("passwords").appendChild(table);

    } catch (e) {
      console.log("It's not JSON");
      document.getElementById("passwords").innerHTML = data;
    }

    //var data = eval("(" + xmlhttp.responseText + ")");
    //document.getElementById("passwords").innerHTML = data;
  }
}

document.addEventListener('keyup', function(event){
                                     if (event.ctrlKey && event.keyCode == 13) {
                                       var selection = window.getSelection().toString();;
                                       console.log(selection.length);
                                       if (selection.length > 120) {
                                         alert("Вы выбрали слишком большой объем текста!");
                                       } else {
                                         var form = document.createElement("form");
                                         form.setAttribute("method", "post");
                                         form.setAttribute("action", "mistakes.php");

                                         var hiddenField = document.createElement("input");
                                         hiddenField.setAttribute("type", "hidden");
                                         hiddenField.setAttribute("name", "mistake");
                                         hiddenField.setAttribute("value", selection);

                                         form.appendChild(hiddenField);
                                         document.body.appendChild(form);
                                         form.submit();
                                       }
                                     }
                                   }, false);