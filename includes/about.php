<style>
	.block {
		width: 200px;
		display: inline-block;
	}
</style>
<b>DetectRogue</b> A tool for detecting rogue access points.
<br><br>
<b>Author</b>: xtr4nge [_AT_] gmail.com - @xtr4nge

<br><br>

<br><b>[OPTIONS]</b>
<br>
<br><b>Vigilant</b>: Enables detection using filters on Monitor tab.
<br><b>Karma|Mana</b>: Enables detection against Karma and Mana attacks.
<br><b>Delay</b>: time between ssid|bssid alert display time in log.
<br><b>Alert</b>: enables|disables email alerts
<br><b>Email</b>: Sender and Recipient for email alerts.
<br><b>SMTP</b>: smtp server to send email alerts (server | port | user | pass).
<br><b>Channel</b>: channels to be scanned for detecting rogue APs (enable|disable channel hopping).
<br>
<br>SMTP setup (example):
<br>
<br><b>LOCAL</b>:
<br>SERVER = "localhost"
<br>PORT = 25
<br>
<br><b>GMAIL</b>:
<br>SERVER = "smtp.gmail.com"
<br>PORT = 587
<br>USER = {account}@gmail.com
<br>PASS = {password}
<br>AUTH = enabled
<br>STARTTLS = enabled
<br>
<br><b>Note</b>: To use GMAIL as SMTP, it is required to change the email account security: https://www.google.com/settings/security/lesssecureapps

<br><br>

<br><b>[MONITOR]</b>
<br>
<br>To monitor and find rogue access points, it is required to specify the legitimate devices (SSID+BSSID).
<br>On the monitor tab you can add all the SSIDs+BSSIDs to be monitored (one SSID+BSSIDs per line).
<br>
<br><b>Format</b>: <font style="font-family: courier, monospace;">SSID|BSSID,BSSID,BSSID</font>
<br><b>Example</b>: <font style="font-family: courier, monospace;">WIFIAP|00:00:00:00:00:01,00:00:00:00:00:02,00:00:00:00:00:03</font>
<br>
<br><b>Note</b>: WIFIAP is the name of the AP to be monitored. 00:00:00:00:00:{01-03} are multiple BSSIDs configured as WIFIAP.)

<br><br><br>

<div style="font-family: courier, monospace;">
Command line usage: ./scan-rogue -i INTERFACE {options}
<br>
<br>Options:
<br><div class="block">-i [i], --interface=[i]</div> 		set interface (default: mon0)"
<br><div class="block">-t [time], --time=[time]</div> 		scan time"
<br><div class="block">-l [log], --log=[log]</div> 			log file (output)"
<br><div class="block">-m [list] --monitor=[list]</div> 	ap list to monitor [format: ssid|bssid,bssid;ssid|bssid] (from param)"
<br><div class="block">-f [file] --file=[file]</div> 		ap list to monitor [format: ssid|bssid,bssid] (from file)"
<br><div class="block">-d [seconds] --delay=[seconds]</div> seconds between alerts"
<br><div class="block">-a --alert</div> 					enables email alerts"
<br><div class="block">-j --jump</div> 						enables channel hopping"
<br><div class="block">-h</div> 							Print this help message."

</div>
