<?php
  include 'util.php';
  include 'search.php';
  epic_tome_header($_SERVER['PHP_SELF']);
?>

<h2 class="clear">Card Search</h2>

<?php

$cards = search_results();
if ($cards == NULL) {
  echo "A database error occured. Please try again later";
  $cards = array();
}
?>

<p id='count'>
  <?php
    $count = count($cards);
    if ($count == 1) {
      echo $count . " card";
    } else {
      echo $count . " cards";
    }
  ?>
  matched your search
</p>

<table border='1'>

<?php

foreach ($cards as $card) {
  $full_type = "{$card['alignment']} {$card['origin']} {$card['card_type']}";
  $is_prize = $card['rarity'] == 'prize';
  $card_name = $card['card_name'];
  $img_src = "scans/" . rawurlencode($card['card_name']) . ".thumb.jpg";
  $card_img = "<img src='$img_src' alt='$card_name' title='$card_name'/>";
  $card_link = "<a href='" . str_replace('thumb', 'full', $img_src) .
                "' target='_blank'>$card_img</a>";
  if ($card['price'] == "99") {
    $card['price'] = "0.50";
  }
?>

<tr class='<?php echo $full_type; ?> card'>

<td class='scan'>
 <table>
  <tr><td>
    <?php echo $card_link;?>
  </td></tr>
  <tr><td>
   <?php
     foreach (array("gift", "restricted", "banned") as $x) {
       if ($card[$x]) {
         echo icon($x);
       }
     }
   ?>
  </td></tr>
 </table>
</td>

<td class='metadata'>
 <table>
  <tr><td>
    Cost: <?php echo icon($card['cost']) ?>
  </td></tr>
  <tr><td>
    Speed:
    <?php
      $img = icon($card['speed']);
      echo ($card['instantaneous'] ? ($img . $img) : $img);
    ?>
  </td></tr>
  <tr><td>
    <?php echo $rarities[$card['rarity']]; ?>
  </td></tr>
  <tr><td>
    <?php echo $set_names[$card['set_name']]; ?>
  </td></tr>
  <tr><td>
    <?php
      echo $card['set_number'] . '/' . $set_sizes[$card['set_name']];
    ?>
  </td></tr>
 </table>
</td>

<td class='data'>
 <table>
  <tr>
    <td class='card_name'>
      <?php echo $card['card_name'];?>
    </td>
    <td class='card_ad'>
      <?php if ($card['qty'] > 0) {
        echo sale_link_from_card($card);
	echo $card['qty'] . " in stock.";
	echo "<br />$" . price_from_cents($card['price']) . " each. ";
      }?>
    </td>
  </tr>

  <tr><td class='full_type'>
    <?php echo ucwords($full_type);?>
  </td></tr>

  <tr><td class='card_text'>
   <p>
    <?php
      echo formatted_card_text($card);
    ?>
   </p>
  </td>

  </tr>

 </table>
</td>

</tr>

<?php
}
?>

</table>

<?php epic_tome_footer(true); ?>
