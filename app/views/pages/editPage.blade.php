@extends('base')

@section('content')

  {{-- Back to page --}}
  <p class='management'>
    <a class='button' href='{{ $original_page_url }}'>
      Retour à la page.
    </a>
  </p>
  
  <div class="row">
    <div class="col-lg-7">
      <h2>Modifier la page</h2>
      <form name="edit_page" method="post" action="" id="edit_page_form">
        <textarea id="inputPane" name="page_content" class="expand">{{ $page_content }}</textarea>
        <p>
          Images: <input type="button" id="uploader" value="Ajouter" />
          <span id="image_list"></span>
        </p>
        <p><button class="button" type="submit">Enregistrer</button></p>
      </form>
    </div>
    <div class="col-lg-5">
      <h2>Exemple</h2>
      <textarea class="expand" disabled="disabled">
Tu peux écrire du texte normalement, mais aussi *en italique*, ou **en gras**.

Laisse une ligne vide pour changer de paragraphe.

## Voici comment afficher un sous-titre dans la page
Et voici du texte après le titre.

## Lien vers une autre page
[Texte affiché](http://www.google.com)

## Une liste, très simple à faire
- A
- B
- C

## Pour les images
![Texte alternatif](mon_image)

## Et pour les connaisseurs...
<span style='background: pink; padding: 5px;'>
  ...le html fonctionne aussi.
</span></textarea>

    </div>
  </div>
  
  <hr>
  
  <div class="row page_content">
    <h1>{{ $page_title }}</h1>
    <div id="previewPane" class="pane">Preview</div>
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
  <script src="{{ URL::to('/') }}/js/showdown.js"></script>
  <script src="{{ URL::to('/') }}/js/showdown-gui.js"></script>
  <script src="{{ URL::to('/') }}/js/upclick.js"></script>
@stop