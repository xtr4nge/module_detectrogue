<? 
/*
    Copyright (C) 2013-2016 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
include "../../login_check.php";
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>FruityWiFi</title>
<script src="../js/jquery.js"></script>
<script src="../js/jquery-ui.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../../../style.css" />

<script src="includes/scripts.js?7"></script>

<script>
$(function() {
    $( "#action" ).tabs();
    $( "#result" ).tabs();
});

</script>

</head>
<body>

<? include "../menu.php"; ?>

<br>

<?
include "../../config/config.php";
include "_info_.php";
include "../../functions.php";


// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_POST["newdata"], "msg.php", $regex_extra);
    regex_standard($_GET["logfile"], "msg.php", $regex_extra);
    regex_standard($_GET["action"], "msg.php", $regex_extra);
    regex_standard($_POST["service"], "msg.php", $regex_extra);
    //regex_standard($_GET["tempname"], "msg.php", $regex_extra);
}

$newdata = $_POST['newdata'];
$logfile = $_GET["logfile"];
$action = $_GET["action"];
//$tempname = $_GET["tempname"];
$service = $_POST["service"];


// DELETE LOG
if ($logfile != "" and $action == "delete") {
    $exec = "rm ".$mod_logs_history.$logfile.".log";
    exec_fruitywifi($exec);
}

// SET MODE
if ($_POST["change_mode"] == "1") {
    $ss_mode = $service;
    $exec = "/bin/sed -i 's/ss_mode.*/ss_mode = \\\"".$ss_mode."\\\";/g' includes/options_config.php";
    exec_fruitywifi($exec);
}

?>

<div class="rounded-top" align="left"> &nbsp; <b><?=$mod_alias?></b> </div>
<div class="rounded-bottom">

    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;version <?=$mod_version?><br>
    
    <?
    $ismoduleup = exec($mod_isup);
    if ($ismoduleup != "") {
        echo "&nbsp; $mod_alias  <font color='lime'><b>enabled</b></font>.&nbsp; | <a href='includes/module_action.php?service=$mod_name&action=stop&page=module'><b>stop</b></a>";
    } else { 
        echo "&nbsp; $mod_alias  <font color='red'><b>disabled</b></font>. | <a href='includes/module_action.php?service=$mod_name&action=start&page=module'><b>start</b></a>"; 
    }
    ?>
    
</div>

<br>

<div id="msg" style="font-size: larger;">
Loading, please wait...
</div>

<div id="body" style="display:none;">

    <div id="result" class="module">
        <ul>
            <li><a href="#tab-output">Output</a></li>
            <li><a href="#tab-options">Options</a></li>
            <li><a href="#tab-monitor">Monitor</a></li>
            <li><a href="#tab-history">History</a></li>
            <li><a href="#tab-about">About</a></li>
        </ul>
        <div id="tab-output">
            <form id="formLogs-Refresh" name="formLogs-Refresh" method="POST" autocomplete="off" action="index.php">
            <input type="submit" value="refresh">
            <br><br>
            <?
                if ($logfile != "" and $action == "view") {
                    $filename = $mod_logs_history.$logfile.".log";
                } else {
                    $filename = $mod_logs;
                }
            
                $data = open_file($filename);
                
                // REVERSE
                //$data_array = explode("\n", $data);
                //$data = implode("\n",array_reverse($data_array));
                
            ?>
            <textarea id="output" class="module-content" style="font-family: courier;"><?=htmlspecialchars($data)?></textarea>
            <input type="hidden" name="type" value="logs">
            </form>
            
        </div>
        <!-- END OUTPUT -->
        
        <!-- OPTIONS -->
        <div id="tab-options" class="history">
            <h4>
                DETECT
            </h4>
            <h5>
                <input id="detectrogue_vigilant" type="checkbox" name="my-checkbox" <? if ($mod_detectrogue_vigilant == "1") echo "checked"; ?> onclick="setCheckbox(this, 'mod_detectrogue_vigilant')" >
                VIGILANT (Monitor Tab)
                <br>
                <input id="detectrogue_karma" type="checkbox" name="my-checkbox" <? if ($mod_detectrogue_karma == "1") echo "checked"; ?> onclick="setCheckbox(this, 'mod_detectrogue_karma')" >
                KARMA|MANA
                
            </h5>
            <hr>
            <h4>
                LOG
            </h4>
            <h5>
                Delay (seconds)
                <br>
                <input id="detectrogue_alert_delay" class="form-control input-sm" placeholder="Delay" value="<?=$mod_detectrogue_alert_delay?>" style="width: 180px; display: inline-block; " type="text" />
                <input class="btn btn-default btn-sm" type="button" value="save" onclick="setOption('detectrogue_alert_delay', 'mod_detectrogue_alert_delay');">
            </h5>
            <hr>
            <h4>
                <input id="detectrogue_alert" type="checkbox" name="my-checkbox" <? if ($mod_detectrogue_alert == "1") echo "checked"; ?> onclick="setCheckbox(this, 'mod_detectrogue_alert')" >
                ALERT
            </h4>
            <hr>
            <h4>
                EMAIL
            </h4>
            <h5>
                <div style="width: 50px; display: inline-block">From</div> <input id="detectrogue_email_from" class="form-control input-sm" placeholder="From" value="<?=$mod_detectrogue_email_from?>" style="width: 180px; display: inline-block; " type="text" />
                <br>
                <div style="width: 50px; display: inline-block">To</div> <input id="detectrogue_email_to" class="form-control input-sm" placeholder="To" value="<?=$mod_detectrogue_email_to?>" style="width: 180px; display: inline-block; " type="text" />
                <input class="btn btn-default btn-sm" type="button" value="save" onclick="setOption('detectrogue_email_from', 'mod_detectrogue_email_from'); setOption('detectrogue_email_to', 'mod_detectrogue_email_to'); setOption('detectrogue_email_subject', 'mod_detectrogue_email_subject');">
            </h5>
            <hr>
            <h4>
                SMTP
            </h4>
            <h5>
                <div style="width: 50px; display: inline-block">Server</div> <input id="detectrogue_smtp_server" class="form-control input-sm" placeholder="Server" value="<?=$mod_detectrogue_smtp_server?>" style="width: 180px; display: inline-block; " type="text" />
                <br>
                <div style="width: 50px; display: inline-block">Port</div> <input id="detectrogue_smtp_port" class="form-control input-sm" placeholder="Port" value="<?=$mod_detectrogue_smtp_port?>" style="width: 180px; display: inline-block; " type="text" />
                <input class="btn btn-default btn-sm" type="button" value="save" onclick="setOption('detectrogue_smtp_server', 'mod_detectrogue_smtp_server'); setOption('detectrogue_smtp_port', 'mod_detectrogue_smtp_port'); ">
                
                <br><br>
                
                <div style="width: 50px; display: inline-block">User</div> <input id="detectrogue_smtp_user" class="form-control input-sm" placeholder="User" value="<?=$mod_detectrogue_smtp_user?>" style="width: 180px; display: inline-block; " type="text" />
                <br>
                <div style="width: 50px; display: inline-block">Pass</div> <input type="password" id="detectrogue_smtp_pass" class="form-control input-sm" placeholder="Port" value="<?=$mod_detectrogue_smtp_pass?>" style="width: 180px; display: inline-block; " type="text" />
                <input class="btn btn-default btn-sm" type="button" value="save" onclick="setOption('detectrogue_smtp_user', 'mod_detectrogue_smtp_user'); setOption('detectrogue_smtp_pass', 'mod_detectrogue_smtp_pass'); ">
                <br>
                <div style="width: 54px; display: inline-block"></div><input id="detectrogue_smtp_auth" type="checkbox" name="my-checkbox" <? if ($mod_detectrogue_smtp_auth == "1") echo "checked"; ?> onclick="setCheckbox(this, 'mod_detectrogue_smtp_auth')" > auth
                <br>
                <div style="width: 54px; display: inline-block"></div><input id="detectrogue_smtp_starttls" type="checkbox" name="my-checkbox" <? if ($mod_detectrogue_smtp_starttls == "1") echo "checked"; ?> onclick="setCheckbox(this, 'mod_detectrogue_smtp_starttls')" > starttls
            </h5>
            <hr>
            <h4>
                CHANNEL
            </h4>
                <div id="channel">
                    <!--<label></label>
                    <br>-->
                    <? $a_channel = explode(",", $mod_detectrogue_channel); ?>
                    <input type="checkbox" name="channel" value="1" onclick="setOptionCheckbox('channel')" <? if (in_array("1", $a_channel)) echo "checked"; ?> > 1
                    <input type="checkbox" name="channel" value="2" onclick="setOptionCheckbox('channel')" <? if (in_array("2", $a_channel)) echo "checked"; ?> > 2
                    <input type="checkbox" name="channel" value="3" onclick="setOptionCheckbox('channel')" <? if (in_array("3", $a_channel)) echo "checked"; ?> > 3
                    <input type="checkbox" name="channel" value="4" onclick="setOptionCheckbox('channel')" <? if (in_array("4", $a_channel)) echo "checked"; ?> > 4
                    <input type="checkbox" name="channel" value="5" onclick="setOptionCheckbox('channel')" <? if (in_array("5", $a_channel)) echo "checked"; ?> > 5
                    <input type="checkbox" name="channel" value="6" onclick="setOptionCheckbox('channel')" <? if (in_array("6", $a_channel)) echo "checked"; ?> > 6
                    <input type="checkbox" name="channel" value="7" onclick="setOptionCheckbox('channel')" <? if (in_array("7", $a_channel)) echo "checked"; ?> > 7
                    <input type="checkbox" name="channel" value="8" onclick="setOptionCheckbox('channel')" <? if (in_array("8", $a_channel)) echo "checked"; ?> > 8
                    <input type="checkbox" name="channel" value="9" onclick="setOptionCheckbox('channel')" <? if (in_array("9", $a_channel)) echo "checked"; ?> > 9
                    <input type="checkbox" name="channel" value="10" onclick="setOptionCheckbox('channel')" <? if (in_array("10", $a_channel)) echo "checked"; ?> > 10
                    <input type="checkbox" name="channel" value="11" onclick="setOptionCheckbox('channel')" <? if (in_array("11", $a_channel)) echo "checked"; ?> > 11
                    <input type="checkbox" name="channel" value="12" onclick="setOptionCheckbox('channel')" <? if (in_array("12", $a_channel)) echo "checked"; ?> > 12
                    <input type="checkbox" name="channel" value="13" onclick="setOptionCheckbox('channel')" <? if (in_array("13", $a_channel)) echo "checked"; ?> > 13
                    <br>
                    <input type="checkbox" name="channel" value="36" onclick="setOptionCheckbox('channel')" <? if (in_array("36", $a_channel)) echo "checked"; ?> > 36
                    <input type="checkbox" name="channel" value="40" onclick="setOptionCheckbox('channel')" <? if (in_array("40", $a_channel)) echo "checked"; ?> > 40
                    <input type="checkbox" name="channel" value="44" onclick="setOptionCheckbox('channel')" <? if (in_array("44", $a_channel)) echo "checked"; ?> > 44
                    <input type="checkbox" name="channel" value="48" onclick="setOptionCheckbox('channel')" <? if (in_array("48", $a_channel)) echo "checked"; ?> > 48
                    <input type="checkbox" name="channel" value="52" onclick="setOptionCheckbox('channel')" <? if (in_array("52", $a_channel)) echo "checked"; ?> > 52
                    <input type="checkbox" name="channel" value="56" onclick="setOptionCheckbox('channel')" <? if (in_array("56", $a_channel)) echo "checked"; ?> > 56
                    <input type="checkbox" name="channel" value="60" onclick="setOptionCheckbox('channel')" <? if (in_array("60", $a_channel)) echo "checked"; ?> > 60
                    <input type="checkbox" name="channel" value="64" onclick="setOptionCheckbox('channel')" <? if (in_array("64", $a_channel)) echo "checked"; ?> > 64
                </div>
        </div>
        <!-- END OPTIONS -->
        
        <!-- FILTER -->
            
        <div id="tab-monitor" class="tab-pane">
            <b>SSID | BSSID</b>
            <br>
            <select class="form-control input-sm" id="pool-bssid" multiple="multiple" style="height: 150px">

            </select>
            <br>
            <input class="form-control input-sm" placeholder="SSID|BSSID,BSSID,..." style="width: 200px; display: inline-block; " id="newBSSIDText" type="text" />
            <input id="add" class="btn btn-default btn-sm" type="submit" value="+" onclick="addListBSSID();">
            <input id="remove" class="btn btn-default btn-sm" type="submit" value="-" onclick="removeListBSSID()">
            
        </div>

        <!-- END FILTER -->
        
        <!-- HISTORY -->

        <div id="tab-history" class="history">
            <input type="submit" value="refresh">
            <br><br>
            
            <?
            $logs = glob($mod_logs_history.'*.log');
            //print_r($a);

            for ($i = 0; $i < count($logs); $i++) {
                $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
                echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=delete&tab=3'><b>x</b></a> ";
                echo $filename . " | ";
                echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=view'><b>view</b></a>";
                echo "<br>";
            }
            ?>
            
        </div>
        
        <!-- END HISTORY -->
        
        <!-- ABOUT -->

        <div id="tab-about" class="history">
            <? include "includes/about.php"; ?>
        </div>

        <!-- END ABOUT -->
        
    </div>

    <div id="loading" class="ui-widget" style="width:100%;background-color:#000; padding-top:4px; padding-bottom:4px;color:#FFF">
        Loading...
    </div>

    <?
    if ($_GET["tab"] == 1) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 1 });";
        echo "</script>";
    } else if ($_GET["tab"] == 2) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 2 });";
        echo "</script>";
    } else if ($_GET["tab"] == 3) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 3 });";
        echo "</script>";
    } else if ($_GET["tab"] == 4) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 4 });";
        echo "</script>";
    } else if ($_GET["tab"] == 5) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 5 });";
        echo "</script>";
    } 
    ?>

</div>

<script type="text/javascript">
    $('#loading').hide();
    $(document).ready(function() {
        $('#body').show();
        $('#msg').hide();
    });
</script>

<script>
    $('.btn-default').on('click', function(){
        $(this).addClass('active').siblings('.btn').removeClass('active');
        param = ($(this).find('input').attr('name'));
        value = ($(this).find('input').attr('id'));
        $.getJSON('../api/includes/ws_action.php?api=/config/module/detectrogue/'+param+'/'+value, function(data) {});
    }); 
</script>

<script>

// EXEC LOAD POOL
loadPoolBSSID()

// EXEC SCAN WiFi
scanRogue()

</script>

</body>
</html>
