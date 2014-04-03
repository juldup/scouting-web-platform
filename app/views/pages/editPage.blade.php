@extends('base')

@section('back_links')
  <p>
    <a href='{{ $original_page_url }}'>
      Retour à la page
    </a>
  </p>
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/ckeditor/ckeditor.js"></script>
  <script>
    CKEDITOR.replace('page_body', {
      language: 'fr',
      extraAllowedContent: 'img[!src,width,height]',
      extraPlugins: 'divarea',
      height: '400px'
    });
    var image_upload_url = "{{ URL::route('ajax_upload_image', array('page_id' => $page_id)) }}";
    var image_remove_url = "{{ URL::route('ajax_remove_image', array('image_id' => 'image_id')) }}";
    var initial_images = [
      @foreach ($images as $image)
        {'image_id': {{ $image->id }}, 'url': '{{ $image->getURL() }}' },
      @endforeach
    ];
  </script>
  <script src="{{ URL::to('/') }}/js/libs/upclick.js"></script>
@stop

@section('content')
  
  <div class="row page_body form-horizontal">
    <form name="edit_page" method="post" action="" id="edit_page_form">
      <h1>{{ $page_title }}</h1>
      <div class="form-group">
        <div class="col-md-12">
          <textarea cols="80" id="page_body" name="page_body" rows="10">{{ $page_body }}</textarea>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-md-2">Images</label>
        <div class="col-md-10">
          <input type="button" id="uploader" value="Ajouter" class="btn btn-default" />
          <span id="image_list"></span>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
          <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
  
@stop