function startCarousel() {
  $('#photo-carousel').carousel({
    interval: 3000
  });
  $('#photo-carousel').carousel('next');
  $('#carousel-controls #carousel-start').hide();
  $('#carousel-controls #carousel-stop').show();
}

function stopCarousel() {
  $('#photo-carousel').carousel('pause');
  $('#carousel-controls #carousel-start').show();
  $('#carousel-controls #carousel-stop').hide();
}