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
      <form name="edit_page" method="post" action="">
        <textarea id="inputPane" name="page_content" class="expand">{{ $page_content }}</textarea>
        <button class="button" type="submit">Enregistrer</button>
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
  
  <div class="row">
    <h1>{{ $page_title }}</h1>
    <div id="previewPane" class="pane">Preview</div>
  </div>
  
  <script src="{{ URL::to('/') }}/js/showdown.js"></script>
  <script src="{{ URL::to('/') }}/js/showdown-gui.js"></script>
@stop