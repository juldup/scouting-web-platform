@extends('base')

@section('additional_javascript')
  <script src="{{ asset('js/guest-book.js') }}"></script>
@stop

@section('back_links')
  @if ($managing)
    <p>
      <a href="{{ URL::route('guest_book') }}">
        Retour au livre d'or
      </a>
    </p>
  @endif
@stop

@section('forward_links')
  @if (!$managing && $can_manage)
    <p>
      <a href="{{ URL::route('edit_guest_book') }}">
        Gérer le livre d'or
      </a>
    </p>
  @endif
@stop

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h1>Livre d'or</h1>
      <p>
        Laissez ici une trace de votre passage sur le site ou dans l'unité.
      </p>
      @include('subviews.flashMessages')
    </div>
  </div>
  @if (!$managing)
    <div class="row">
      <div class="col-md-12">
        <a class='btn btn-default guest-book-button'
           @if (Session::has('_old_input')) style="display: none;" @endif
           >
          Ajouter un message au livre d'or
        </a>
        <div class="form-horizontal well guest-book-form"
             @if (!Session::has('_old_input')) style="display: none;" @endif
             >
          {{ Form::open(array('url' => URL::route('guest_book_submit'))) }}
            <legend>
              Nouveau message pour le livre d'or
            </legend>
            <div class="form-group">
              {{ Form::label('author', 'Qui êtes-vous ?', array('class' => 'control-label col-md-2')) }}
              <div class="col-md-8">
                {{ Form::text('author', null, array('class' => 'form-control', 'placeholder' => "Votre nom, qui vous êtes par rapport à l'unité")) }}
              </div>
            </div>
            <div class="form-group">
              {{ Form::label('body', 'Message', array('class' => 'control-label col-md-2')) }}
              <div class="col-md-8">
                {{ Form::textarea('body', null, array('class' => 'form-control', 'rows' => 6, 'placeholder' => "Entrez ici votre message")) }}
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-8 col-md-offset-2">
                {{ Form::submit('Soumettre', array('class' => 'btn btn-primary')) }}
                <a href="" class="btn btn-default guest-book-cancel">
                  Annuler
                </a>
              </div>
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  @endif
  <div class="vertical-divider"></div>
  <div class="row">
    @if (count($guest_book_entries))
      @foreach ($guest_book_entries as $entry)
        <div class="col-md-12">
          <div class="well">
            <legend>
              <div class="row">
                <div class="col-md-10">
                  {{ Helper::dateToHuman($entry->created_at) }} – Message de {{{ $entry->author }}}
                </div>
                @if ($managing)
                  <div class="col-md-2 text-right">
                    <a class="btn-sm btn-danger" href="{{ URL::route('edit_guest_book_delete', array('entry_id' => $entry->id)) }}">
                      Supprimer
                    </a>
                  </div>
                @endif
              </div>
            </legend>
            {{ Helper::rawToHTML($entry->body) }}
          </div>
        </div>
      @endforeach
    @else
      <div class="col-md-12">
        <p>Le livre d'or est vide :-(</p>
        @if (!$managing)
          <p>Soyez le premier à y laisser un petit mot.</p>
        @endif
      </div>
    @endif
  </div>
@stop