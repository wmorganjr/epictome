<?php

$rares = array(11,16,24,26,29,31,33,39,40,41,42,43,44,45,51,52,55,58,59,60,62,64,67,68,71,72,74,76,81,84,85,88,89,92,95,102,104,116,118,121,123,124,127,128,129,132,135,136,137,139,145,149,156,158,160,169,171,175,177,178,180,183,188,189,190,195,198,199,200,203,205,209,211,212,217,218,223,226,229,234,235,236,238,240,245,246,254,256,260,261,264,268,277,286,287,289,291,292,294,299);

$fasts = array(38 ,251, 57 ,197 ,152 ,144 ,34 ,214 ,290 ,23 ,80 ,103 ,206 ,98 ,202 ,69 ,70 ,7 ,17 ,119 ,270 ,93 ,242 ,15 ,300 ,138 ,225 ,48 ,79 ,288 ,19 ,228 ,186 ,224 ,151 ,282 ,275 ,36 ,120 ,96 ,37 ,267 ,90 ,237 ,107 ,247 ,106 ,97 ,243 ,2 ,126 ,272 ,54 ,221 ,274 ,65 ,47 ,257 ,259 ,53 ,21 ,99 ,30 ,220 ,157 ,78 ,122 ,285 ,162 ,193 ,143 ,91 ,35 ,125 ,216 ,1 ,194 ,244 ,210 ,187 ,63 ,283 ,297 ,140 ,241 ,253 ,250 ,86 ,192 ,108 ,213 ,10 ,141 ,196 ,94 ,32 ,222 ,75 ,179 ,231);

$slows = array(279 ,61 ,227 ,296 ,8 ,163 ,66 ,77 ,215 ,113 ,6 ,73 ,109 ,22 ,4 ,170 ,146 ,201 ,14 ,133 ,174 ,276 ,167 ,273 ,181 ,18 ,249 ,280 ,114 ,5 ,27 ,101 ,284 ,161 ,100 ,110 ,265 ,164 ,117 ,165 ,142 ,266 ,12 ,233 ,28 ,50 ,166 ,298 ,185 ,159 ,232 ,230 ,134 ,49 ,82 ,3 ,204 ,83 ,155 ,9 ,131 ,184 ,293 ,111 ,176 ,281 ,112 ,191 ,219 ,271 ,46 ,168 ,150 ,87 ,173 ,258 ,269 ,148 ,248 ,262 ,154 ,182 ,115 ,252 ,255 ,263 ,25 ,56 ,20 ,208 ,147 ,239 ,153 ,278 ,105 ,130 ,295 ,207 ,172 ,13);

$pack = array();

$pack_set = $_GET["pack"];

$rare_seed = rand(0, 99);
$fast_seed = rand(0, 99);
$slow_seed = rand(0, 99);

if (is_numeric($pack_set)) {
  $pack_num = (int) $pack_set;
  $rare_seed = ($pack_num / 10000) % 100;
  $fast_seed = ($pack_num / 100) % 100;
  $slow_seed = ($pack_num / 1) % 100;
}

array_push($pack, $rares[$rare_seed]);

for ($i = 0; $i < 7; $i++) {
  array_push($pack, $fasts[($fast_seed + $i) % 100]);
}

for ($i = 0; $i < 7; $i++) {
  array_push($pack, $slows[($slow_seed + $i) % 100]);
}

$query = "SELECT * FROM cards WHERE FALSE ";
foreach ($pack as $set_number) {
  $query .= "OR set_number = " . $set_number . " ";
}
$query .= ";";

$link = mysql_connect('mysql.thespoilsonline.com', 'epictcg', 'epicpass');
if (!$link) {
    header("HTTP/1.0 500 Internal Server Error");
    die("Could not establish a connection to the server");
}

$db = mysql_select_db("epictcg", $link);
if (!$db) {
    header("HTTP/1.0 500 Internal Server Error");
    die("Could not switch to the epictcg database");
}

$result = mysql_query($query);

if (!$result) {
    header("HTTP/1.0 500 Internal Server Error");
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    mysql_close($link);
    die($message);
}

$pack_num = $rare_seed * 10000 +
            $fast_seed * 100 +
	    $slow_seed;

$doc = new DomDocument('1.0', "UTF-8");
$root = $doc->createElement('cards');
$root = $doc->appendChild($root);
$pack_node = $doc->createElement("pack_id");
$root->appendChild($pack_node);
$pack_node->appendChild($doc->createTextNode($pack_num));


while ($row = mysql_fetch_assoc($result)) {
    $occ = $doc->createElement("card");
    $occ = $root->appendChild($occ);
    foreach ($row as $fieldname => $fieldvalue) {
        if ($fieldvalue == "\0") {
            $fieldvalue = "false";
        }
        if ($fieldvalue == "\1") {
            $fieldvalue = "true";
        }
        $fieldvalue = $fieldvalue . "";
        $child = $doc->createElement($fieldname);
        $child = $occ->appendChild($child);
        $value = $doc->createTextNode($fieldvalue);
        $value = $child->appendChild($value);
    }
}

$contents = $doc->saveXML();
header("Content-Type: text/xml");
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
echo $contents;

mysql_free_result($result);

mysql_close($link);

?>
