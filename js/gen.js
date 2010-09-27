var criteria = {
  alignment : ["good", "evil", "wild", "unaligned"],
  origin : ["constructed", "natural", "magical"],
  cost : ["free", "action"],
  speed : ["fast", "paced", "build"],
  type : ["champion", "object", "event"]
};

var MAX_VERT_CARDS = 18;
var VERT_GRID = 25;
var HOZ_GRID = 25;
var CARD_WIDTH = 125;

var card_widths = {
  small : 100,
  medium: 125,
  large: 150
};

var CARD_SIZE = "medium";

jQuery.fn.sort = function() {
  return this.pushStack(jQuery.makeArray([].sort.apply(this, arguments)));
}; 

function sort() {
  var classes = criteria[$("#sort_criterion").val().toLowerCase()];

  var z_index = 50;
  var x_pos = 0;
  for (var i = 0; i < classes.length; i++) {
    var c = classes[i];
    var flag = false;
    var cards = 0;
    $("#pool ." + c).sort(function(a, b) {
       return parseInt(a.getAttribute('name')) -
              parseInt(b.getAttribute('name'));
    }).each(function() {
      if (cards > MAX_VERT_CARDS) {
        cards = 0;
	x_pos += CARD_WIDTH;
      }
      $(this).css({
        position: 'absolute',
        left: x_pos,
        top: cards * VERT_GRID,
        zIndex: z_index
      });
      flag = true;
      cards++;
      z_index++;
    });
    if (flag) {
      x_pos += CARD_WIDTH + HOZ_GRID;
    }
  }
}

function src_from_card_name(card_name) {
  var size = "thumb";
  if (CARD_WIDTH == 125) size = "mid";
  if (CARD_WIDTH == 150) size = "big";

  return "scans/" + card_name.replace("\?", "") + "." + size + ".jpg";
}

function make_images_draggable() {
  $("#pool img").draggable({
    stack: {
	  group: 'img',
	  min: 50
	},
	grid: [HOZ_GRID, VERT_GRID],
	stop: function(event, ui) {
	  var img = $(this)[0];
	  var center = img.offsetLeft + CARD_WIDTH / 2 + 250;

	  var sb_left = $("#sb")[0].offsetLeft;
	  var sb_right = sb_left + CARD_WIDTH;

	  if (center > sb_left && center < sb_right) {
	    $(this).appendTo("#sb");
	    restack();
	  }

	  var trash_left = $("#trash")[0].offsetLeft;
	  var trash_right = trash_left + CARD_WIDTH;

	  if (center > trash_left && center < trash_right) {
	    $(this).appendTo("#trash");
	    restack();
	  }

	}
  });
}

function open_pack(set_name) {
  var rand = Math.floor(Math.random() * 2000000);
  var url = "gen_pack.php?seed=" + rand + "&set_name=" + set_name;
  $.ajax({
    url: url,
	cache: false,
	contentType: "json",
	success: function(cards) {
	  $.each(cards, function(idx, card) {
	    $(document.createElement("img"))
	      .appendTo("#pool")
	      .attr('src', src_from_card_name(card.card_name))
	      .addClass("card")
	      .addClass(card.alignment)
	      .addClass(card.origin)
	      .addClass(card.card_type)
	      .addClass(card.speed)
	      .addClass(card.cost)
	      .attr('name', card.set_number);
	  });
	  make_images_draggable();
	  sort();
	  count_pool();
	},
	error: function(a,b,c) {
	  $(document.createElement("p"))
	      .text("There was an error retrieving the pack. Yell at Will.")
	      .appendTo("#pool");
	}
  });
}

function card_name_from_src(src) {
  var ret = src.replace("scans/", "").replace(".mid.jpg", "")
                                     .replace(".big.jpg", "")
                                     .replace(".thumb.jpg", "");
  if (ret == "Can't or Won't") {
    return "Can't or Won't?";
  }
  return ret;
}

function restack() {
  count_pool();
  $("#trash, #sb").each(function() {
    var zIndex = 50;
	var count = 0;
    $(this).children("img").each(function() {
	  $(this).css({
	    left: "0px",
	    top: count * VERT_GRID,
	    zIndex: zIndex
	  });
	  count++;
	  zIndex++;
	});
  });
}

function count_pool() {
  var len = $("#pool img.card").get().length;
  var size = len > 30 ? "light" : len < 30 ? "light" : "ok";
  $("#pool_count").text(len + '/30').removeClass('heavy light ok').addClass(size);

}

function force_save() {
    var format = $("#save_format").val();
	var deck = new Object();
	var sb = new Object();
	$("#pool img").each(function() {
	  var card_name = card_name_from_src($(this).attr('src'));
	  if (deck[card_name]) {
	    deck[card_name]++;
	  } else {
	    deck[card_name] = 1;
	  }
	});

	$("#sb img.card").each(function() {
	  var card_name = card_name_from_src($(this).attr('src'));
	  if (sb[card_name]) {
	    sb[card_name]++;
	  } else {
	    sb[card_name] = 1;
	  }
	});

	var loc = "save.php?format=" + escape(format);
	for (var card_name in deck) {
	  loc += "&" + escape(card_name) + "=" + deck[card_name];
	}
	for (var card_name in sb) {
	  loc += "&" + escape(card_name) + "sideboard=" + sb[card_name];
	}

	location.href = loc;
}

function deck_error() {
  if ($("#pool img").length != 30) {
    return "Deck must contain exactly 30 cards.";
  } else if ($("#sb img.card").length != 15 &&
             $("#sb img.card").length != 0) {
    return "Sideboard must contain either 0 or 15 cards.";
  }
  return 0;
}

function generate() {
  $("#deck_error").remove();
  $(".card").remove();

  var num_packs = $("#num_packs_core").val();
  for (var i = 0; i < num_packs; i++) {
    open_pack('core');
  }

  var num_packs = $("#num_packs_tw").val();
  for (var i = 0; i < num_packs; i++) {
    open_pack('tw');
  }
}

$(function() {
  $("#pool").text("");

  $("img.card").live("mousedown", function() {
    $("#zoom").attr('src', $(this).attr('src')
	                              .replace('.thumb.jpg', '.full.jpg')
				      .replace('.big.jpg', '.full.jpg')
	                              .replace('.mid.jpg', '.full.jpg'));
  }).live("dblclick", function() {
    var dest = $("#sb img.card").length < 15 ? "#sb" : "#trash";
    $(this).appendTo(dest);
	restack();
  });
  $("#sb img.card, #trash img.card").live("mousedown", function() {
    $(this).appendTo($("#pool")).css('zIndex', 100);
	restack();
  });
  $("#trash, #sb").droppable({
    accept: "#pool img",
	activeClass: 'ui-state-highlight',
	hoverClass: 'ui-state-hover',
	drop: function(event, ui) {
	  $(ui.draggable).draggable('option', 'cancel', '#sb img, #trash img')
	                 .appendTo($(this));
	  // Droppable doesn't automatically clear the sb highlight
	  $("#sb").removeClass('ui-state-highlight');
	  restack();
	}
  });

  $("#generate").live("click", function() {
    generate();
  });

  $("#sort_button, #sort_criterion option").live("click", function() {
    sort();
  });

  $("#save").live("click", function() {
    var msg = deck_error();
	if (msg) {
	  $(document.createElement("p")).text(msg)
	                                .attr('id', 'deck_error')
	                                .insertBefore("#save_p");
	  $(document.createElement("button")).appendTo("#deck_error")
					     .text("Save anyway")
					     .attr('id', 'force_button');
	  $("#force_button").live("click", function() {
	    force_save();
	  });
	} else {
	  force_save();
	}

  });

  $("#card_size").change(function() {
    var command = $(this).val();
	if (command == "Small Cards") {
	  CARD_WIDTH = 100;
	}
	if (command == "Medium Cards") {
	  CARD_WIDTH = 125;
	}
	if (command == "Large Cards") {
	  CARD_WIDTH = 150;
	}

	$("img.card").css({
	  height: (CARD_WIDTH * 1.4) + "px",
	  width: CARD_WIDTH + "px"
	});

	$("img.card").each(function() {
	  $(this).attr('src',
	                 src_from_card_name(
		           card_name_from_src(
			     $(this).attr('src'))));
	});

	VERT_GRID = CARD_WIDTH / 5;
	HOZ_GRID = VERT_GRID;
	$("#sb, #trash").css('width', (CARD_WIDTH + 4) + "px");
	$("#sb_img, #trash_img").css({
	  width: (CARD_WIDTH + 3) + "px",
	  height: (CARD_WIDTH + 3) + "px"
	});
	$("#pool img").draggable('option', 'grid', [HOZ_GRID, VERT_GRID]);
	restack();
	sort();
  });

});

