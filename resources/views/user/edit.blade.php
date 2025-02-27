@extends('base')
<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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

use App\Models\Parameter;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;

?>

@section('title')
  @if ($action == 'email')
    Changer mon adresse e-mail
  @elseif ($action == 'password')
    Changer mon mot de passe
  @elseif ($action == 'section')
    Changer ma section par défaut
  @else
    Mon compte d'utilisateur
  @endif
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('content')
  
  @if (Session::has('success_message'))
    <p class='alert alert-success'>{{ Session::get('success_message') }}</p>
  @endif

  <div class="well">
    <legend>Vos données personnelles</legend>
  
    <div class="row">
      <label class='col-md-2 text-right'>Nom d'utilisateur</label>
      <div class='col-md-3'>
        <p>{{{ $user->username }}}</p>
      </div>
    </div>

    <div class="row">
      <label class='col-md-2 text-right'>Adresse e-mail</label>
      <div class='col-md-3'>
        <p>{{{ $user->email }}}</p>
      </div>
      <div class="col-md-7">
        <p>
          <a class="btn-sm btn-primary" href="{{ URL::route('edit_user_email') }}#modification">Changer mon adresse e-mail</a>
        </p>
        @if (!$user->verified)
          <p>
            <a class="btn-sm btn-primary" href="{{ URL::route('user_resend_validation_link') }}">Me renvoyer le lien de validation</a>
          </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label class='col-md-2 text-right'>Mot de passe</label>
      <div class='col-md-3'>
        <p>******</p>
      </div>
      <div class="col-md-7">
        <p>
          <a class='btn-sm btn-primary' href="{{ URL::route('edit_user_password') }}#modification">Changer mon mot de passe</a>
        </p>
      </div>
    </div>

    <div class="row">
      <label class='col-md-2 text-right'>Section par défaut</label>
      <div class='col-md-3'>
        <p>{{{ $user->getDefaultSection()->name }}}</p>
      </div>
      <div class="col-md-7">
        <p>
          <a class="btn-sm btn-primary" href="{{ URL::route('edit_user_section') }}#modification">Changer ma section par défaut</a>
        </p>
      </div>
    </div>

  </div>
    
  @if ($action)
  
    <div class="row">
      <div class="col-md-12">
        <div class="well">
          <a name='modification'></a>
          <legend>
            @if ($action == 'email')
              Modification de l'adresse e-mail
            @elseif ($action == 'password')
              Modification du mot de passe
            @elseif ($action == 'section')
              Modification de la section par défaut
            @endif
          </legend>

          {!! Form::open(array('class' => 'form-horizontal')) !!}
            @if ($action != 'section')
              <div class="form-group">
                {!! Form::label('old_password', "Mot de passe actuel", array('class' => "col-md-2 control-label")) !!}
                <div class='col-md-3'>
                  {!! Form::password('old_password', array('class' => 'form-control')) !!}
                </div>
              </div>
              @if ($errors->first('old_password'))
                <div class="form-group">
                  <div class='col-md-8 col-md-offset-2'>
                    <p class="alert alert-danger">{{ $errors->first('old_password') }}</p>
                  </div>
                </div>
              @endif
            @endif
            @if ($action == 'email')
              <div class="form-group">
                {!! Form::label('email', "Nouvelle adresse", array('class' => "col-md-2 control-label")) !!}
                <div class='col-md-3'>
                  {!! Form::text('email', '', array('class' => 'form-control')) !!}
                </div>
              </div>
              @if ($errors->first('email'))
                <div class="form-group">
                  <div class='col-md-8 col-md-offset-2'>
                    <p class="alert alert-danger">{{ $errors->first('email') }}</p>
                  </div>
                </div>
              @endif
            @elseif ($action == 'password')
              <div class="form-group">
                {!! Form::label('password', "Mot de passe désiré", array('class' => "col-md-2 control-label")) !!}
                <div class='col-md-3'>
                  {!! Form::password('password', array('class' => 'form-control')) !!}
                </div>
              </div>
              @if ($errors->first('password'))
                <div class="form-group">  
                  <div class='col-md-8 col-md-offset-2'>
                    <p class="alert alert-danger">{{ $errors->first('password') }}</p>
                  </div>
                </div>
              @endif
            @elseif ($action == 'section')
              <div class="form-group">
                {!! Form::label('default_section', "Section par défaut", array('class' => "col-md-2 control-label")) !!}
               <div class='col-md-3'>
                  {!! Form::select('default_section', $sections, $user->default_section, array('class' => 'form-control')) !!}
                </div>
              </div>
            @endif
            <div class="form-group">
              <div class='col-md-offset-2 col-md-10'>
                {!! Form::submit('Valider', array('class' => "btn btn-primary")) !!}
              </div>
            </div>
          {!! Form::close() !!}

        </div>
      </div>
    </div>
  @endif
  
@stop