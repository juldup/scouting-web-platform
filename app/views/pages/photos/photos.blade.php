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
              <td><a class="btn-sm btn-default" href="{{ URL::route('download_photo_album', array('album_id' => $album->id)) }}">Télécharger l'album</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  
  
  @if (count($photos))  
    <div class="row">
      <div class="col-md-12">
        <h2>{{ $current_album->name }}</h2>
        <div id="photo-carousel" class="carousel slide">
          @if (count($photos) > 1)
            <div id="carousel-controls">
              <a id="carousel-start" href="javascript:startCarousel();" title="Diaporama">
                <span class="glyphicon glyphicon-play"></span>
              </a>
              <a id="carousel-stop" href="javascript:stopCarousel();" style="display: none;">
                <span class="glyphicon glyphicon-pause"></span>
              </a>
            </div>
          @endif
          <div class="carousel-inner">
            <?php $photoCounter = 1; ?>
            @foreach ($photos as $photo)
              <div class="item @if ($photo == $photos[0]) active @endif">
                <div class="image-wrapper-outer">
                  <div class="image-wrapper-inner">
                    <a href="{{ $photo->getOriginalURL(); }}">
                      <img data-src="{{ $photo->getPreviewURL(); }}" />
                    </a>
                  </div>
                </div>
                @if ($photo->caption)
                  <div class="carousel-caption">{{ $photo->caption }}</div>
                @endif
                <div class="carousel-counter">{{ $photoCounter++ }} / {{ count($photos) }}</div>
              </div>
            @endforeach
          </div>
          @if (count($photos) > 1)
            <a class="carousel-control left" href="#photo-carousel" data-slide="prev">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a class="carousel-control right" href="#photo-carousel" data-slide="next">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          @endif
        </div>
      </div>
    </div>
    
    <div class="row">
      <?php $photoIndex = 0; ?>
      @foreach ($photos as $photo)
        <div class="col-md-2 photo-thumbnail-wrapper">
          <div class="photo-thumbnail">
            <a href="javascript:showPhoto({{ $photoIndex++ }})">
              <img src="{{ $photo->getThumbnailURL(); }}" />
            </a>
          </div>
        </div>
      @endforeach
    </div>
  @endif
  
@stop