<?php

include_once('util.php');

function search_results() {
  if (! ($link = db_connect())) {
    return NULL;
  }

  $clauses = build_clauses($link);
  $query = build_query($clauses);

  if (! ($result = mysql_query($query))) {
    return NULL;
  }

  $results = result_to_array($result);

  mysql_free_result($result);
  mysql_close($link);

  return $results;
}

function build_clauses($link) {
  $clauses = "TRUE";

  $clauses = add_exact_params($clauses, $link);
  $clauses = add_approx_params($clauses, $link);
  
  if ($_GET["instock"]) {
    $clauses .= " AND stock.qty > 0";
  }

  return $clauses;
}

function add_exact_params($clauses, $link) {
  $exact_params = array("alignment", "origin", "card_type", "cost", "set_name",
                        "rarity", "speed", "instantaneous", "gift");

  foreach ($exact_params as $param_name) {
    $param = $_GET[$param_name];
    if (is_array($param)) {
      if (count($param) > 0 && strlen($param[0]) > 0) {
        $clauses .= build_disjunction($param_name, $param, $link);
      }
    } elseif (strlen($param) > 0) {
      $param = mysql_real_escape_string($param, $link);
      $clauses .= " AND cards.$param_name = " . bool_or_quote($param);
    }
  }

  return $clauses;
}

function build_disjunction($param_name, $param_values, $link) {
  $rtn = " AND (FALSE";

  foreach ($param_values as $value) {
    $value = mysql_real_escape_string($value, $link);
    $rtn .= " OR cards.$param_name = " . bool_or_quote($value);
  }

  $rtn .= ")";
  return $rtn;
}

function add_approx_params($clauses, $link) {
  $approx_params = array("card_name", "text");

  foreach ($approx_params as $param_name) {
    $param = $_GET[$param_name];
    if ($param) {
      $param = mysql_real_escape_string($param, $link);
      $clauses .= " AND $param_name LIKE '%$param%'";
    }
  }

  return $clauses;
}

function build_query($clauses) {
  $join_on = "cards.set_name = stock.set_name AND " .
             "cards.set_number = stock.set_number";
  $table = "cards LEFT JOIN stock ON ($join_on)";
  $query = "SELECT * FROM $table WHERE $clauses ORDER BY card_name;";

  return $query;
}

function result_to_array($result) {
  $rtn = array();

  while ($row = mysql_fetch_assoc($result)) {
    $arr = array();
    foreach ($row as $fieldname => $fieldvalue) {
      $arr[$fieldname] = bool_to_string($fieldvalue);
    }
    array_push($rtn, $arr);
  }

  return $rtn;
}

function bool_or_quote($x) {
  if ($x == "true") {
    return 1;
  } elseif ($x == "false") {
    return 0;
  } else {
    return "'$x'";
  }
}

function bool_to_string($x) {
  if ($x == "\0") {
    return false;
  } elseif ($x == "\1") {
    return true;
  } else {
    return $x;
  }
}

?>
