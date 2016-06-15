function setCheckbox(item, param) {
    if (document.getElementById(item.id).checked) {
        value = "1";
    } else {
        value = "0";
    }
    $.getJSON('../api/includes/ws_action.php?api=/config/module/detectrogue/'+param+'/'+value, function(data) {});
}

function setOption(item, param) {
	value = $("#"+item).val();
    $.getJSON('../api/includes/ws_action.php?api=/config/module/detectrogue/'+param+'/'+value, function(data) {});
}

function setRadio(item, param) {
    value = document.getElementById(item.id).value
	//console.log(document.getElementById(item.id).checked);
	//console.log(document.getElementById(item.id).value);
    $.getJSON('../api/includes/ws_action.php?api=/config/module/detectrogue/'+param+'/'+value, function(data) {});
}

// STORE OPTIONS (Checkbox)
function setOptionCheckbox(id) {
    data = []
    $("#"+id+" input:checked").each(function() {                    
        //console.log($(this).attr('value'))
        data.push($(this).attr('value'))
        })
    
    value = data.join(",")
    console.log(value)
    
    //value = $("#"+id).val()
    console.log(id + "|" + value)
    
    $.getJSON('../api/includes/ws_action.php?api=/config/module/detectrogue/mod_detectrogue_'+id+'/'+value, function(data) {});
    
}

function scanRogue(args) {
	$.ajax({
        type: 'GET',
        //url: '../api/includes/ws_action.php',
        url: 'includes/ws_action.php',
        data: 'api=/scan/rogue',
        dataType: 'json',
        success: function (data) {
            //console.log(data);
            //$('#output').html('');
			
            $.each(data, function (index, value) {
                //console.log(value);
                
                v_timestamp = value[0]
                v_bssid = value[1]
                v_ssid = value[2]
                v_channel = value[3]

				content = "<div>"+v_timestamp+" | "+v_bssid+" | "+v_ssid+" | "+v_channel+"</div>"
				
				$("#rogue").append(content)
                
            });
        }
    });
}

function loadPoolBSSID(args) {
    $.ajax({
        type: 'GET',
        url: 'includes/ws_action.php',
        data: 'api=/pool/bssid',
        dataType: 'json',
        success: function (data) {
            //console.log(data);

            $.each(data, function (index, value) {
                //console.log(value);
                
                // ACTION START
                if (checkValue(value) != true) {
                    $('<option/>').attr('value',value).text(value).appendTo('#pool-bssid');
                }
                
                // ACTION END
                
            });
        }
    });
}

function setPoolBSSID(value)
{
    $.ajax({
        type: 'GET',
        url: 'includes/ws_action.php',
        data: 'api=/pool/bssid/'+value,
        dataType: 'json',
        success: function (data) {
            console.log(data);
        }
    });
}

function delPoolBSSID(value)
{
    $.ajax({
        type: 'GET',
        //url: '../api/includes/ws_action.php',
        url: 'includes/ws_action.php',
        data: 'api=/pool/bssid/'+value+"/del",
        dataType: 'json',
        success: function (data) {
            console.log(data);
        }
    });
}

function addListBSSID() {    
    var value = $('#newBSSIDText').val();
    
    if (checkValue(value) != true && value != "") {
        $('<option/>').attr('value',value).text(value).appendTo('#pool-bssid');
        setPoolBSSID(value);
    }
}

function removeListBSSID() {
    value = $('option:selected',$select).text();
    
    var $select = $('#pool-bssid');
    $('option:selected',$select).remove();
    
    delPoolBSSID(value);
}

function checkValue(MAC) {
    var exists = false; 
    $('#pool-station option').each(function(){
        //alert(this.text)
        //if (this.value == MAC) {
        if (this.text == MAC) {
            //alert(this.text)
            exists = true;
        }
    });
    return exists
}