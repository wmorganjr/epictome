var criteria = {
  alignment : ["good", "evil", "wild", "unaligned"],
  origin : ["constructed", "natural", "magical"],
  cost : ["free", "action"],
  speed : ["fast", "paced", "build"],
  type : ["champion", "object", "event"]
};

var card_widths = {
  thumb : 100,
  mid: 125,
  big: 150
};

var max_cards_per_column = {
  thumb: 25,
  mid: 22,
  big: 18
}

function card_width() {
  return card_widths[$("#card_size").val()];
}

function grid() {
  return card_width() / 5;
}

jQuery.fn.sort = function() {
  return this.pushStack(jQuery.makeArray([].sort.apply(this, arguments)));
}; 

function compare_set_numbers(a, b) {
 return parseInt(a.getAttribute('set_number')) -
        parseInt(b.getAttribute('set_number'));
}

function sort_pool() {
  var classes = criteria[$("#sort_criterion").val().toLowerCase()];

  var column_max = max_cards_per_column[$("#card_size").val()];

  var x_pos = 0;
  $.each(classes, function(idx, c) {
    var contains_at_least_one_card = false;
    $("#pool ." + c)
      .sort(compare_set_numbers)
      .each(function(i) {
        if (i > 0 && i % column_max == 0) {
          x_pos += card_width();
        }
        $(this).css({
          left: x_pos,
          top: (i % column_max) * grid(),
          zIndex: 50 + i
        });
        contains_at_least_one_card = true;
      });

      if (contains_at_least_one_card) {
        x_pos += card_width() + grid();
      }
  });
}

function src_from_card_name(card_name) {
  return "scans/" + escape(card_name) + "." + $("#card_size").val() + ".jpg";
}

function card_name_from_src(src) {
  return /scans\/(.*)\.(thumb|mid|big)\.jpg/.exec(unescape(src))[1];
}

function make_images_draggable() {
  $("#pool img").draggable({
    stack: "img",
    grid: [grid(), grid()]
  });
}

function create_card(card) {
  $(document.createElement("img"))
    .attr('src', src_from_card_name(card.card_name))
    .addClass("card")
    .addClass(card.alignment)
    .addClass(card.origin)
    .addClass(card.card_type)
    .addClass(card.speed)
    .addClass(card.cost)
    .attr('set_number', card.set_number);
}

function open_pack(set_name) {
  $.getJSON(
    "gen_pack.php", {
      seed : Math.floor(Math.random() * 2000000),
      set_name : set_name
    }, function(cards) {
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
          sort_pool();
          count_pool();
    });
}

function count_pool() {
  var len = $("#pool img.card").get().length;
  var size = len > 30 ? "light" : len < 30 ? "light" : "ok";
  $("#pool_count")
    .text(len + '/30')
    .removeClass('heavy light ok')
    .addClass(size);
}

function force_save() {
  var format = $("#save_format").val();
  var deck = new Object();
  var sb = new Object();
  $("#pool img.card").each(function() {
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

function resize_stacks() {
  $("#sb, #trash").css('width', (card_width() + 4) + "px");
  $("#sb_img, #trash_img").css({
    width: (card_width() + 3) + "px",
    height: (card_width() + 3) + "px"
  });
  $("#sb").css('right', (card_width() + 4) + "px");
}

function align_first_in_stacks() {
  $("#sb .card").first()
    .css('margin-top', -1 * (card_width() + 3) + "px");
  $("#trash .card").first()
    .css('margin-top', -1 * (card_width() + 3) + "px");
}

function move_card_from_pool(card, dest) {
  if (! dest) {
    dest = $("#sb img.card").length < 15 ? $("#sb") : $("#trash");
  }

  card.css('margin-top', (card_width() * -1.4 + grid()) + "px")

  card.appendTo(dest);

  align_first_in_stacks();

  count_pool();
}

function update_for_new_size() {
  $("img.card").css({
    height: (card_width() * 1.4) + "px",
    width: card_width() + "px"
  });

  $("img.card").each(function() {
    $(this).attr('src',
      src_from_card_name(card_name_from_src($(this).attr('src'))));
  });

  resize_stacks();
  align_first_in_stacks();

  $("#pool img").draggable('option', 'grid', [grid(), grid()]);
  sort_pool();
}

$(function() {
  $("#pool").text("");

  $("img.card").live("mouseover", function() {
    $("#zoom").attr('src', $(this).attr('src')
                             .replace(/(thumb|mid|big).jpg/, 'full.jpg'));
  }).live("dblclick", function() {
    move_card_from_pool($(this));
  });

  $("#sb img.card, #trash img.card").live("mousedown", function() {
    $(this)
      .appendTo("#pool")
      .css({ "margin-top" : "0px",
             "z-index" : 200});
    align_first_in_stacks();
    count_pool();
  });

  $("#trash, #sb").droppable({
    accept: "#pool img",
    activeClass: "ui-state-highlight",
    hoverClass: "ui-state-hover",
    drop: function(event, ui) {
      move_card_from_pool($(ui.draggable), $(this));
    }
  });

  resize_stacks();

  $("#generate").live("click", function() {
    generate();
  });

  $("#sort_button, #sort_criterion option").live("click", function() {
    sort_pool();
  });

  $("#save").live("click", function() {
    var msg = deck_error();
    if (msg) {
      $("#deck_error, #force_button").html("");
      $(document.createElement("p"))
        .text(msg)
        .attr('id', 'deck_error')
        .insertBefore("#save_p");

      $(document.createElement("button"))
        .appendTo("#deck_error")
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
    update_for_new_size();
  });
});

