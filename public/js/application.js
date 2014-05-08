
/**************************************************
 * KEEP SESSION ALIVE
 **************************************************/

setInterval(function() {
  $.ajax({
    url: keepaliveURL,
  }).done(function() {
  });
}, 60000);

/**************************************************
 * LOGIN
 **************************************************/

// Make sure there is a login and password
function checkLogin() {
  if (document.login.login_username.value === '') {
    document.login.login_username.focus();
    document.login.login_username.select();
    return false;
  }
  if (document.login.login_password.value === '') {
    document.login.login_password.focus();
    document.login.login_password.select();
    return false;
  }
  return true;
}
// Connecte l'utilisateur s'il a entré un pseudo et un mot de passe
function submitLogin() {
  if (checkLogin()) document.login.submit();
}
// Valide la connexion si 'enter' est pressé
function checkEnter(e) {
  if (e.which === 13 || e.keyCode === 13) submitLogin();
}

/**************************************************
 * CALENDAR
 **************************************************/

$().ready(function() {
  $("#download-calendar-button").click(function(event) {
    $("#download-calendar-button").hide();
    $("#download-calendar-form").show();
    return false;
  });
});

/**************************************************
 * CHECKBOXES -> SWITCHES
 **************************************************/

// Convert all checkboxes to switches
$().ready(function() {
  $('input[type="checkbox"]').bootstrapSwitch();
  $('input[type="checkbox"]').bootstrapSwitch('setOnLabel', 'Oui');
  $('input[type="checkbox"]').bootstrapSwitch('setOffLabel', 'Non');
});

/**************************************************
 * CLICKABLE (BIG-TARGET) ELEMENTS
 **************************************************/

$().ready(function() {
  // Clickable elements trigger big target if clicked directly (not when a child is clicked)
  $(".clickable").bind("click", function(event) {
    // Check if the clicked element is not a child
    if ($(event.target)[0] == $(this)[0]) {
      var a = $(this).find('a[href]');
      var inNewWindow = a.attr('target') === "_blank";
      if (inNewWindow) {
        window.open(a.attr('href'));
      } else {
        window.location = a.attr('href');
      }
      return false;
    }
  });
  // Clickable-no-default elements trigger big target (when clicked or when a child is clicked)
  $(".clickable-no-default").bind("click", function(event) {
    var a = $(this).find('a[href]');
    var inNewWindow = a.attr('target') === "_blank";
    if (inNewWindow) {
      window.open(a.attr('href'));
    } else {
      window.location = a.attr('href');
    }
    event.preventDefault();
  });
});

/**************************************************
 * HELP BUTTON
 **************************************************/

$().ready(function() {
  $(".help-toggle-button .help-badge").click(function() {
    $(this).closest(".help-wrapper").find(".help-content").slideToggle();
    return false;
  });
});
