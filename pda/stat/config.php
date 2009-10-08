<?
$STATS_CONF["dbname"]="pda";
$STATS_CONF["sqlhost"]="localhost";
$STATS_CONF["sqluser"]="webcarumba";
$STATS_CONF["sqlpassword"]="6Fasj6FQ7d";

$STATS_CONF["adminpassword"]="62692d2334c78845f00d70eac10f51f1";
$STATS_CONF["sqlserver"]="MySql";

// E-Mail, used as login to administration console
$STATS_CONF["cnsoftwarelogin"]="bigsan@mail.ru2";

// The name of your web-server
$COUNTER["servername"]="php.carumba.ru";

// Storing up the statistics.
$COUNTER["savelog"]=30;

// Do not count jumps from network excludeip/excludemask
$COUNTER["excludeip"]="0.0.0.0";
$COUNTER["excludemask"]="255.255.255.255";

// Time difference between local and server time in seconds
$COUNTER["timeoffset"]=0;

// Time delay of starting midnight_calc procedure in seconds
$COUNTER["mnoffset"]=900;

// Turn off CNStats authorization
// yes - turn off
// no - do not turn off
$COUNTER["disablepassword"]="no";

// Send errors reports to E-Mail (E-Mail is set
// in option $STATS_CONF["cnsoftwarelogin"]
$COUNTER["senderrorsbymail"]="yes";

// Adjust tables and diagrammes to the necessary resolution.
// Values may be 800 or 1024
$COUNTER["resolution"]=800;
?>