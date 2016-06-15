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

include "../../../functions.php";

$regex_extra = $regex_extra."/";

// Checking POST & GET variables [regex]...
if ($regex == 1) {
    regex_standard($_POST["api"], "../../../msg.php", $regex_extra);
    regex_standard($_GET["api"], "../../../msg.php", $regex_extra);
	regex_standard($_POST["token"], "../../../msg.php", $regex_extra);
    regex_standard($_GET["token"], "../../../msg.php", $regex_extra);
}

if (isset($_GET["token"])) {
	$token = $_GET["token"];
} else {
	include "../../../login_check.php";
	include "../../../config/config.php";
	$token = $api_token;
}


require("ws.php");
$ws = new WebServiceExtendedPlus($token);
$ws->login();

$api = $_GET["api"];
$api = explode("/", $api);

// SCAN RECON
if (sizeof($api) == 3 and $api[1] == "scan" and $api[2] == "rogue")
{
	echo $ws->scanRogue();
}

// POOL BSSID

if (sizeof($api) == 3 and $api[1] == "pool" and $api[2] == "bssid")
{
	echo $ws->getPoolBSSID();
}

if (sizeof($api) == 4 and $api[1] == "pool" and $api[2] == "bssid")
{
	echo $ws->setPoolBSSID($api[3]);
}

if (sizeof($api) == 5 and $api[1] == "pool" and $api[2] == "bssid" and $api[4] == "del")
{
	echo $ws->delPoolBSSID($api[3]);
}

?>