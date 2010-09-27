<?php

include_once('util.php');

header('Content-type: application/json');

$seed = $_GET['seed'];

if (! $seed) {
  $seed = 0;
}

srand($seed);

$set_name = $_GET['set_name'];

$pack = array();

if ($set_name == 'core') {

  $rares = array(11,16,24,26,29,31,33,39,40,41,42,43,44,45,51,52,55,58,59,60,62,64,67,68,71,72,74,76,81,84,85,88,89,92,95,102,104,116,118,121,123,124,127,128,129,132,135,136,137,139,145,149,156,158,160,169,171,175,177,178,180,183,188,189,190,195,198,199,200,203,205,209,211,212,217,218,223,226,229,234,235,236,238,240,245,246,254,256,260,261,264,268,277,286,287,289,291,292,294,299);

  $fasts = array(38 ,251, 57 ,197 ,152 ,144 ,34 ,214 ,290 ,23 ,80 ,103 ,206 ,98 ,202 ,69 ,70 ,7 ,17 ,119 ,270 ,93 ,242 ,15 ,300 ,138 ,225 ,48 ,79 ,288 ,19 ,228 ,186 ,224 ,151 ,282 ,275 ,36 ,120 ,96 ,37 ,267 ,90 ,237 ,107 ,247 ,106 ,97 ,243 ,2 ,126 ,272 ,54 ,221 ,274 ,65 ,47 ,257 ,259 ,53 ,21 ,99 ,30 ,220 ,157 ,78 ,122 ,285 ,162 ,193 ,143 ,91 ,35 ,125 ,216 ,1 ,194 ,244 ,210 ,187 ,63 ,283 ,297 ,140 ,241 ,253 ,250 ,86 ,192 ,108 ,213 ,10 ,141 ,196 ,94 ,32 ,222 ,75 ,179 ,231);

  $slows = array(279 ,61 ,227 ,296 ,8 ,163 ,66 ,77 ,215 ,113 ,6 ,73 ,109 ,22 ,4 ,170 ,146 ,201 ,14 ,133 ,174 ,276 ,167 ,273 ,181 ,18 ,249 ,280 ,114 ,5 ,27 ,101 ,284 ,161 ,100 ,110 ,265 ,164 ,117 ,165 ,142 ,266 ,12 ,233 ,28 ,50 ,166 ,298 ,185 ,159 ,232 ,230 ,134 ,49 ,82 ,3 ,204 ,83 ,155 ,9 ,131 ,184 ,293 ,111 ,176 ,281 ,112 ,191 ,219 ,271 ,46 ,168 ,150 ,87 ,173 ,258 ,269 ,148 ,248 ,262 ,154 ,182 ,115 ,252 ,255 ,263 ,25 ,56 ,20 ,208 ,147 ,239 ,153 ,278 ,105 ,130 ,295 ,207 ,172 ,13);

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

} elseif ($set_name == 'tw') {
  $stops = array(162, 152, 99, 97, 47, 32, 25, 174, 94);

  $wraths = array(164, 93, 84, 73, 71, 44, 45, 40, 11);

  $rares = array(3, 12, 17, 30, 33, 34, 36, 39, 48, 49, 64, 66, 67, 78, 80, 90, 98, 101, 102, 109, 110, 113, 116, 126, 133, 134, 147, 148, 149, 154, 163, 167, 168, 171);

  $ultras = array(22, 27, 28, 43, 57, 61, 62, 63, 74, 82, 95, 105, 117, 118, 130, 151, 153, 160, 173);

  $frees = array(96, 172, 46, 81, 10, 59, 55, 53, 106, 68, 56, 132, 35, 125, 150, 37, 69, 156, 124, 38, 23, 50, 170, 75, 16, 15);

  $slows = array(1, 7, 166, 137, 159, 146, 161, 21, 19, 145, 138, 135, 104, 103, 143, 131, 144, 8, 18, 58, 26, 29, 115, 139, 158, 123, 114, 42, 88, 5, 91, 52, 85, 157);

  $fasts = array(6, 169, 13, 41, 136, 107, 129, 122, 86, 141, 24, 165, 72, 119, 87, 31, 79, 92, 108, 128, 65, 111, 89, 77, 121, 127, 54, 14, 70, 100, 112, 4, 2, 140, 142, 120, 76, 155, 51, 60, 83, 20, 9);

  $stops_seed = rand(0, count($stops) - 1);
  $wraths_seed = rand(0, count($wraths) - 1);
  $rares_seed = rand(0, count($rares) * 3 + count($ultras) - 1);
  $frees_seed = rand(0, count($frees) - 1);
  $slows_seed = rand(0, count($slows) - 1);
  $fasts_seed = rand(0, count($fasts) - 1);

  array_push($pack, $stops[$stops_seed]);
  array_push($pack, $wraths[$wraths_seed]);

  for ($i = 0; $i < 3; $i++) {
    array_push($pack, $frees[($frees_seed + $i) % count($frees)]);
  }

  if ($rares_seed < 3 * count($rares)) {
    array_push($pack, $rares[$rares_seed % count($rares)]);
  } else {
    array_push($pack, $ultras[$rares_seed - (3 * count($rares))]);
  }

  for ($i = 0; $i < 4; $i++) {
    array_push($pack, $slows[($slows_seed + $i) % count($slows)]);
  }

  for ($i = 0; $i < 5; $i++) {
    array_push($pack, $fasts[($fasts_seed + $i) % count($fasts)]);
  }

} else {
  echo json_encode("unrecognized set name");
  exit();
}


$query = "SELECT * FROM cards WHERE set_name = '$set_name' AND set_number IN (-1";

foreach ($pack as $set_number) {
  $query .= ", $set_number";
}

$query .= ")";

if (! ($link = db_connect())) {
  die("Database Error");
}

if (! $result = mysql_query($query)) {
  die("Database Error");
}

$list = array();
while ($row = mysql_fetch_assoc($result)) {
  $arr = array();
  foreach ($row as $fieldname => $fieldvalue) {
    if ($fieldvalue == "\0") {
      $fieldvalue = false;
    }
    if ($fieldvalue == "\1") {
      $fieldvalue = true;
    }
    $arr[$fieldname] = $fieldvalue;
  }
  array_push($list, $arr);
}

echo json_encode($list);

