<?php
$mod_name="detectrogue";
$mod_version="1.0";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs="$log_path/$mod_name.log"; 
$mod_logs_history="$mod_path/includes/logs/";
$mod_panel="show";
$mod_type="service";
$mod_alias="DetectRogue";

# OPTIONS
$mod_detectrogue_alert="0";
$mod_detectrogue_alert_delay="5";
$mod_detectrogue_smtp_server="localhost";
$mod_detectrogue_smtp_port="25";
$mod_detectrogue_smtp_user="";
$mod_detectrogue_smtp_pass="";
$mod_detectrogue_smtp_auth="0";
$mod_detectrogue_smtp_starttls="0";
$mod_detectrogue_channel="1,2,3,4,5,6,7,8,9,10,11,12";
$mod_detectrogue_email_from="";
$mod_detectrogue_email_to="";

# EXEC
$bin_sudo = "/usr/bin/sudo";
$bin_iptables = "/sbin/iptables";
$bin_awk = "/usr/bin/awk";
$bin_grep = "/bin/grep";
$bin_sed = "/bin/sed";
$bin_cat = "/bin/cat";
$bin_echo = "/bin/echo";
$bin_ln = "/bin/ln";
$bin_arp = "/usr/sbin/arp";
$bin_rm = "/bin/rm";
$bin_cp = "/bin/cp";

# ISUP
$mod_isup="ps auxww | grep -iEe 'scan-rogue' | grep -v -e grep";
?>
