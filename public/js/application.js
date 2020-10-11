/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * This script is loaded on all pages of the website and provides some
 * functionalities used on most pages
 */

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

// The following induces a delay on obfuscated forms to prevent bots from using them
$().ready(function() {
  var timeout = 3;
  var updateSubmitButton = function() {
    if (timeout === 0) {
      $(".obfuscated-form").each(function() {
        $(this).find('input[type="submit"], button[type="submit"]').prop('disabled', false);
        $(this).find('input[type="submit"]').each(function() {
          $(this).val($(this).data('text'));
        });
        $(this).find('button[type="submit"]').each(function() {
          $(this).html($(this).data('text'));
        });
        $(this).prop('action', $(this).data('action-url'));
      });
    } else {
      $('.obfuscated-form').find('button[type="submit"]').html(timeout + " secondes");
      $('.obfuscated-form').find('input[type="submit"]').val(timeout + " secondes");
      timeout--;
      setTimeout(updateSubmitButton, 1000);
    }
  };
  updateSubmitButton();
});

/**************************************************
 * CALENDAR
 **************************************************/

$().ready(function() {
  $("#download-calendar-button").click(function(event) {
    $("#download-calendar-button").hide();
    $("#download-calendar-form").show();
    return false;
  });
  $("body").click(function(event) {
    $(".calendar-event-details").hide();
  });
  $(".calendar-event").click(function(event) {
    var description = $(this).closest(".calendar-event-wrapper").find(".calendar-event-details");
    if (!description.is(":visible")) {
      setTimeout(function() {
        description.show();
      }, 0);
    }
  });
});

/**************************************************
 * CHECKBOXES -> SWITCHES
 **************************************************/

// Convert all checkboxes to switches
$().ready(function() {
  $('input[type="checkbox"]:not(.no-bootstrap-switch)').bootstrapSwitch();
  $('input[type="checkbox"]:not(.no-bootstrap-switch)').bootstrapSwitch('setOnLabel', 'Oui');
  $('input[type="checkbox"]:not(.no-bootstrap-switch)').bootstrapSwitch('setOffLabel', 'Non');
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

/**************************************************
 * COMMENTS
 **************************************************/

$().ready(function() {
  $(".add-comment-button").click(function() {
    // Add comment form
    var referentId = $(this).data('referent');
    var referentType = $(this).data('referent-type');
    var $commentForm = $($("#comment-prototype").html());
    $(this).before($commentForm);
    $commentForm.attr('action', $commentForm.attr('action').replace('REFERENT_ID', referentId).replace('REFERENT_TYPE', referentType));
    $commentForm.find('textarea').focus();
    // Remove this button
    var $addCommentButton = $(this)
    $addCommentButton.hide();
    // Set submit action
    $commentForm.submit(function() {
      var body = $commentForm.find('textarea[name=body]').val().trim();
      if (!body) return false;
      // Disable submit button
      $commentForm.find('input[type=submit]').prop('disabled', true);
      $.ajax({
        url: $commentForm.attr('action'),
        type: 'POST',
        data: {
          body: body,
        },
        success: function(data) {
          // Add comment to list of comments
          $commentForm.after('<div class="comment"><span class="comment-meta">À l\'instant, <span class="comment-username">' + currentUserName + '</span> a écrit &nbsp;:</span> <span class="comment-body">' + body + '</span></div>');
          // Remove form
          $commentForm.remove();
          // Show comment button again
          $addCommentButton.show();
        },
        error: function() {
          // Show error message
          alert("Une erreur est servenue. Votre commentaire n'a pas été enregistré.");
          // Re-enable submit button
          $commentForm.find('input[type=submit]').prop('disabled', false);
        }
      });
      return false;
    });
    return false;
  });
  
  // Set show hidden comment action
  $(".show-hidden-comments").click(function() {
    $(this).parent().find(".comment").show();
    $(this).remove();
  });
});
