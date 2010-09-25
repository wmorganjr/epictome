<?php

$set_sizes = array(
  'core' => 300,
  'prize' => 22,
  'tw' => 174,
  'precon' => 8
);

$set_names = array(
  'core' => "Core Set",
  'prize' => "Prize",
  'tw' => "Time Wars",
  'precon' => "Precon"
);

$rarities = array(
  'common' => 'Common',
  'rare' => 'Rare',
  'ultra' => 'Ultra Rare'
);

function formatted_card_text($card) {
  $icons = array("FAST", "BUILD", "PACED", "OFFENSE",
                 "DEFENSE", "EXPEND", "FREE", "FLIP");

  $text = $card['text'];
  if ($card['card_type'] == 'champion') {
    $text .= "\nOFFENSE {$card['offense']}/DEFENSE {$card['defense']}";
  }
  $text = str_replace("\n", "</p><p>", $text);
  $text = str_replace('\1/', icon("action"), $text);

  foreach ($icons as $icon) {
    $text = str_replace($icon, icon(strtolower($icon)), $text);
  }
                   
  return $text;
}

function icon($name) {
  return "<img src='icons/$name.png' alt='$name' title='$name' />";
}

function sale_link_from_card($card) {
  $price = price_from_cents($card['price']);
  return "<div class='product'>
            <input value='{$card['card_name']}'
                   class='product-title' type='hidden' />
            <input value='$price' class='product-price'
                   type='hidden' />
            <div title='Add to cart' role='button' tabindex='0'
                 class='googlecart-add-button'></div></div>";
}

function price_from_cents($cents) {
  $c = $cents % 100;
  return floor($cents / 100) . '.' . ($c == 0 ? "00" : $c);
}

function epic_tome_header($current) {
  $title = get_title($current);
  $navbar = build_navbar($current);

  echo <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" >

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link type='text/css' rel='stylesheet' href='css/undohtml.css' />
<link type='text/css' rel='stylesheet' href='css/json.css' />
<link type='text/css' rel='stylesheet' href='css/front.css' />
<title>Epic Tome: $title</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<div id='container'>
<h1>
<a href='/'><img src='site.png' alt='Epic Tome' /></a>
<span>POWER TOOLS FOR EPIC TCG</span>
</h1>

$navbar

END;
}

function build_navbar($current) {
  $navs = array(
    'SEARCH' => "/",
    'BUY' => "/buy.html",
    'SEALED' => "/sealed.html",
    'DRAFT' => "/draft.html",
    'ABOUT' => "/about.html"
  );
  $navbar = "<ul>";
  foreach ($navs as $text => $href) {
    $is_current = $current == $href ||
                  ($href == "/" && $current == "/display.php") ||
                  ($href == "/" && $current == "/index.html");
    $class = $is_current ? "current" : "";
    $navbar .= "<li><a class='$class' href='$href'>$text</a></li>";
  }
  $navbar .= "</ul>";

  return $navbar;
}

function get_title($current) {
  $titles = array(
    '/index.html' => 'Visual Spoiler for Epic TCG',
    '/display.php' => 'Search Results',
    '/buy.html' => 'Buy Epic TCG singles',
    '/sealed.html' => 'Sealed Deck Generator',
    '/draft.html' => 'Online Draft Application',
    '/about.html' => 'About'
  );

  return $titles[$current];
}

function epic_tome_footer($is_store) {
  echo <<<END
<div id="footer">
<p>
The Epic Trading Card Game and all card images are © Copyright 2009 <a href="http://epictcg.com">Epic Trading Card Game</a> and redistributed with permission.
</p>
<p>
All other content is © Copyright 2010 <a href="http://epictcg.com/forums/member.php?u=3">Will Morgan</a> unless other specified.
</p>
</div>
END;

echo "</div>";

if ($is_store) {
  echo <<<END
<script id='googlecart-script' type='text/javascript' src='https://checkout.google.com/seller/gsc/v2_2/cart.js?mid=724071367116412' integration='jscart-wizard' post-cart-to-sandbox='false' currency='USD' productWeightUnits='LB' hide-cart-when-empty="true"></script>
END;
}

echo "</body></html>";

}

?>

