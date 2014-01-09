@extends('base')

@section('back_links')
  <p>
    <a href='{{ $original_page_url }}'>
      Retour à la page
    </a>
  </p>
@stop

@section('content')
  
  <div class="row page_content">
    <form name="edit_page" method="post" action="" id="edit_page_form">
      <h1>{{ $page_title }}</h1>
      <script src="{{ URL::route('home') }}/ckeditor/ckeditor.js"></script>
      <textarea cols="80" id="page_content" name="page_content" rows="10">{{ $page_content }}</textarea>
      <p>
        Images: <input type="button" id="uploader" value="Ajouter" />
        <span id="image_list"></span>
      </p>
      <p><button class="button" type="submit">Enregistrer</button></p>
    </form>
    <script>
      CKEDITOR.replace('page_content', {
        language: 'fr',
        extraAllowedContent: 'img[!src,width,height]',
        extraPlugins: 'divarea',
        height: '400px'
      });
    </script>
  </div>
  
  <script>
    var image_upload_url = "{{ URL::route('ajax_upload_image', array('page_id' => $page_id)) }}";
    var image_remove_url = "{{ URL::route('ajax_remove_image', array('image_id' => 'image_id')) }}";
    var initial_images = [
      @foreach ($images as $image)
        {'image_id': {{ $image->id }}, 'url': '{{ $image->getURL() }}' },
      @endforeach
    ];
  </script>
  <script src="{{ URL::to('/') }}/js/upclick.js"></script>
@stop