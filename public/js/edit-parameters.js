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
 * This script is present on the website parameter management page
 */

$().ready(function() {
  // Logo preview
  $("input[type='file'][name='logo']").change(function(event) {
    // Put selected image in logo preview
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.onload = function(theFile) {
      return function(event) {
        var src = event.target.result;
        $("img.website-logo-preview").attr('src', src);
      };
    }(file);
    reader.readAsDataURL(file);
  });
  // Icon preview
  $("input[type='file'][name='icon']").change(function(event) {
    // Put selected image in logo preview
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.onload = function(theFile) {
      return function(event) {
        var src = event.target.result;
        $("img.website-icon-preview").attr('src', src);
      };
    }(file);
    reader.readAsDataURL(file);
  });
  // Document categories delete/add
  $('.document-category-remove').click(function() {
    $(this).closest(".document-category-row").remove();
  })
  $('.document-category-add').click(function() {
    var newElement = $('.document-category-prototype').clone(true);
    $(this).closest(".row").before(newElement);
    newElement.removeClass('document-category-prototype');
    newElement.show();
  });
  // Verified e-mail senders delete/add
  $('.safe-email-remove').click(function() {
    $(this).closest(".safe-email-row").remove();
  })
  $('.safe-email-add').click(function() {
    var newElement = $('.safe-email-row-prototype').clone(true);
    $(this).closest(".row").before(newElement);
    newElement.removeClass('safe-email-row-prototype');
    newElement.show();
  });
  // Add confirmation on photos public option
  $('.photos-public-checkbox').on('switch-change', function () {
    if ($(this).is(":checked")) {
      if (!confirm("Attention à la question du droit à l'image. Es-tu sûr de vouloir continuer ?")) {
        $(this).prop("checked",false).trigger("change");
      }
    }
  });
  // Enable/disable registration button according to automatic registration
  setTimeout(function() {
    if ($("[name='registration_automatic'").prop('checked')) {
      $("[name='registration_active']").bootstrapSwitch('toggleDisabled',true,true);
      $(".registration_automatic_only").removeClass('semi-invisible');
    } else {
      $(".registration_automatic_only").addClass('semi-invisible');
      $(".registration_automatic_only").find('input').prop('disabled', 'disabled');
    }
  }, 10);
  $("[name='registration_automatic'").on('change.bootstrapSwitch', function(event, state) {
    var checked = $("[name='registration_automatic'").prop('checked') ? true : false;
    if ($("[name='registration_active']").prop('disabled') == !checked) {
      $("[name='registration_active']").bootstrapSwitch('toggleDisabled',true,true);
    }
    if (checked) {
      $(".registration_automatic_only").removeClass('semi-invisible');
      $(".registration_automatic_only").find('input').prop('disabled', false);
    } else {
      $(".registration_automatic_only").addClass('semi-invisible');
      $(".registration_automatic_only").find('input').prop('disabled', 'disabled');
    }
  });
  // Enable/disable registration button according to advanced registration
  setTimeout(function() {
    if ($("[name='advanced_registrations'").prop('checked')) {
      $(".advanced-registration-only").removeClass('semi-invisible');
    } else {
      $(".advanced-registration-only").addClass('semi-invisible');
    }
  }, 10);
  $("[name='advanced_registrations'").on('change.bootstrapSwitch', function(event, state) {
    if ($("[name='advanced_registrations'").prop('checked')) {
      $(".advanced-registration-only").removeClass('semi-invisible');
      $(".advanced-registration-only").find('input').prop('disabled', false);
    } else {
      $(".advanced-registration-only").addClass('semi-invisible');
      $(".advanced-registration-only").find('input').prop('disabled', 'disabled');
    }
  });
  
});
