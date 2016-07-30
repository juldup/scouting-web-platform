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
 * This script is present on the photo album page
 */

$().ready(function() {
  // Create carousel on photos
  $('#photo-carousel').carousel({interval: 3000});
  // Initially pause the carousel
  $('#photo-carousel').carousel('pause');
  
  // Preloading of adjacent pictures for faster transitions
  $.fn.extend({
    // Applied on an div.item, pre-loads images contained in it and its neighbours
    loadImagesAround: function() {
      // Get siblings
      var next = $(this).next();
      var nextOfNext = next.next();
      var prev = $(this).prev();
      var prevOfPrev = prev.prev();
      // Load image
      $(this).loadImage();
      next.loadImage();
      nextOfNext.loadImage();
      prev.loadImage();
      prevOfPrev.loadImage();
    },
    // Preload images contained in it (i.e. copy data-src to src)
    loadImage: function() {
      $(this).find('img').each(function() {
        if (!$(this).attr('src')) {
          $(this).attr('src', $(this).data('src'));
        }
      });
    }
  })
  
  // Preload 3 first photos and 2 last photos
  $('#photo-carousel .item').first().loadImage();
  $('#photo-carousel .item').first().next().loadImage();
  $('#photo-carousel .item').first().next().next().loadImage();
  $('#photo-carousel .item').last().loadImage();
  $('#photo-carousel .item').last().prev().loadImage();
  
  // When a picture is change, load neighbor images
  $('#photo-carousel').on('slide.bs.carousel', function(event) {
    var next = $(event.relatedTarget);
    next.loadImagesAround();
  });
  
  // Open another photo album
  $(".photo-album-row").click(function() {
    var url = $(this).find("a.photo-album-link").attr('href');
    if (url) window.location = url;
  });
  
  // Show comments when sliding
  $('#photo-carousel').bind('slide.bs.carousel', function(event) {
    // Get loading photo index
    var index = $(event.relatedTarget).index();
    // Hide all comments
    $('.photo-comments-wrapper .photo-comments').slideUp();
    // Show comment for the loading photo
    $('.photo-comments-wrapper .photo-comments:nth-child(' + (index + 1) + ')').slideDown();
  });
});

/**
 * Starts the photo carousel
 */
function startCarousel() {
  $('#photo-carousel').carousel('cycle');
  $('#photo-carousel').carousel('next');
  $('#carousel-controls #carousel-start').hide();
  $('#carousel-controls #carousel-stop').show();
}

/**
 * Pauses the photo carousel
 */
function stopCarousel() {
  $('#photo-carousel').carousel('pause');
  $('#carousel-controls #carousel-start').show();
  $('#carousel-controls #carousel-stop').hide();
}

/**
 * Shows a particular photo and stops the carousel
 */
function showPhoto(photoIndex) {
  $('#photo-carousel').carousel(photoIndex);
  stopCarousel();
}
