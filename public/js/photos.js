$().ready(function() {
  $('#photo-carousel').carousel({interval: 3000});
  $('#photo-carousel').carousel('pause');
  
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
  
  $('#photo-carousel').on('slide.bs.carousel', function(event) {
    var next = $(event.relatedTarget);
    next.loadImagesAround();
  });
});

function startCarousel() {
  console.log("start carousel");
  $('#photo-carousel').carousel('cycle');
  $('#photo-carousel').carousel('next');
  $('#carousel-controls #carousel-start').hide();
  $('#carousel-controls #carousel-stop').show();
}

function stopCarousel() {
  $('#photo-carousel').carousel('pause');
  $('#carousel-controls #carousel-start').show();
  $('#carousel-controls #carousel-stop').hide();
}

function showPhoto(photoIndex) {
  $('#photo-carousel').carousel(photoIndex);
  stopCarousel();
}
