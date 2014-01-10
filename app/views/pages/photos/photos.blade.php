@extends('base')

@section('title')
  Photos
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/photos.js"></script>
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Photos {{ $user->currentSection->de_la_section }}</h1>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <h2>Albums</h2>
      <table class="table table-striped table-hover">
        <tbody>
          @foreach($albums as $album)
            <tr>
              <td>
                @if ($album == $current_album)
                  <strong>{{ $album->name }}</strong>
                @else
                  <a href='#'>{{ $album->name }}</a>
                @endif
              </td>
              <td>{{ $album->photo_count }} {{ $album->photo_count > 1 ? "photos" : "photo" }}</td>
              <td><a class="btn-sm btn-default">Télécharger l'album</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      <h2>Photos</h2>
      <div id="carousel-controls">
        <a id="carousel-start" href="javascript:startCarousel();">Start</a>
        <a id="carousel-stop" href="javascript:stopCarousel();" style="display: none;">Stop</a>
      </div>
      <div id="photo-carousel" class="carousel slide">
        <div class="carousel-inner">
          @foreach ($photos as $photo)
            <div class="item @if ($photo = $photos[0]) active @endif">
              <div class="image-wrapper-outer">
                <div class="image-wrapper-inner">
                  <img src="{{ $photo->getThumbnailURL(); }}" />
                </div>
              </div>
              <div class="carousel-caption">Caption 1</div>
            </div>
          @endforeach
        </div>
        <a class="carousel-control left" href="#photo-carousel" data-slide="prev">&lsaquo;</a>
        <a class="carousel-control right" href="#photo-carousel" data-slide="next">&rsaquo;</a>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-12">
      @foreach($photos as $photo)
        <a href="#" class="photo-thumbnail">
          <img src="{{ $photo->getThumbnailURL(); }}" />
        </a>
      @endforeach
    </div>
  </div>
  
@stop