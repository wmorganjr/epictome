<?php
  include('set_name_mapping.php');
  $dt = date("F j, Y, g:i:s a");
  $format = str_replace("_", " ", $_GET["format"]);

  $ext = ".txt";
  if ($format == "Magic Workstation") {
    $ext = ".mwDeck";
  }

  header("Content-Type: application/octet-stream");
  header("Content-Disposition: attachment; filename=\"Sealed $dt$ext\"");

  if ($format == "Magic Workstation") {
    echo("// Deck file for Magic Workstation (http://www.magicworkstation.com)\r\n");
    echo("// LINKED WITH: Epic;TW\r\n");
  }

  foreach ($_GET as $card => $quantity) {
    if ($card != "format") {
      $card = str_replace("_", " ", $card);
      if ($format == "LackeyCCG") {
        echo($quantity . "	" . $card . "\r\n");
      } elseif ($format == "Magic Workstation") {
        if (strstr($card, "sideboard")) {
	  $card = str_replace("sideboard", "", $card);
	  echo("SB: ");
	} else {
          echo("    ");
	}
	  if ($card == "Mr  Hyde") {
	    $card = "Mr. Hyde";
	  }

          $set_name = $set_name_mapping[$card];
	  $a = "[EPC]";
	  if ($set_name == 'tw') {
	    $a = "[TW]";
	  }
	  
	  $b = "{EPIC}";
	  if ($set_name == 'tw') {
	    $b = "{TW}";
	  }

	  echo $quantity . " $a " . $card . " $b\r\n";
      }
    }
  }

?>
