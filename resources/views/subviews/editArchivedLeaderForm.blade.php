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
use App\Models\Section;

?>

<?php if (!isset($form_legend)) $form_legend = "Membre"; ?>
<?php if (!isset($form_id)) $form_id = "member_form"; ?>

<div id="{{ $form_id }}" class='well member-form-wrapper'
     @if (!Session::has('_old_input')) style="display: none;" @endif
     >
  <legend>{{{ $form_legend }}}</legend>
  {!! Form::open(array('files' => true, 'url' => $submit_url)) !!}
    <div class="form-group">
      <div class="col-md-12">
        <div class="text-center">
          {!! Form::submit('Enregistrer', array('class' => 'btn btn-primary')) !!}
          <a class='btn btn-default dismiss-form' href="javascript:dismissMemberForm()">Fermer</a>
        </div>
      </div>
    </div>
    {!! Form::hidden('member_id') !!}
    <div class="row">
      <div class="col-md-6 form-horizontal">
        <div class="form-group">
          {!! Form::label('first_name', "Prénom", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            {!! Form::text('first_name', '', array('class' => 'form-control')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('last_name', "Nom", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('last_name', '', array('class' => 'form-control')) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('gender', "Sexe", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::select('gender', array('M' => 'Garçon', 'F' => 'Fille'), '', array('class' => 'form-control')) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('phone_member', "GSM", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            {!! Form::text('phone_member', '', array('class' => 'form-control medium')) !!}
            Confidentiel : {!! Form::checkbox('phone_member_private', '1', '', array()) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('email_member', "Adresse e-mail", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('email_member', '', array('placeholder' => "L'adresse e-mail n'est jamais publiée", 'class' => 'form-control')) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('totem', "Totem", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('totem', '', array('class' => 'form-control')) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('quali', "Quali", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('quali', '', array('class' => 'form-control')) !!}</div>
        </div>
      </div>
      <div class="col-md-6 form-horizontal">
        <div class="form-group">
          {!! Form::label('section', "Section", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::select('section', Section::getSectionsForSelect(), '', array('class' => 'form-control medium')) !!}</div>
        </div>
        <div class='form-group'>
          {!! Form::label('leader_name', "Nom d'animateur", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('leader_name', '', array('placeholder' => "Nom utilisé dans sa section", 'class' => 'form-control')) !!}</div>
        </div>
        <div class='form-group'>
          {!! Form::label('leader_in_charge', "Animateur responsable", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            <div class="checkbox">
              {!! Form::checkbox('leader_in_charge', 1, '', array()) !!}
            </div>
          </div>
        </div>
        <div class='form-group'>
          {!! Form::label('leader_description', "Description de l'animateur", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::textarea('leader_description', '', array('placeholder' => "Petite description qui apparaitra sur la page des animateurs", 'class' => 'form-control', 'rows' => 3)) !!}</div>
        </div>
        <div class='form-group'>
          {!! Form::label('leader_role', "Rôle de l'animateur", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('leader_role', '', array('placeholder' => "Rôle particulier dans le staff", 'class' => 'form-control')) !!}</div>
        </div>
        <div class='form-group'>
          {!! Form::label('picture', "Photo", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            {!! Form::file('picture', array('class' => 'btn btn-default')) !!}
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="text-center">
          {!! Form::submit('Enregistrer', array('class' => 'btn btn-primary')) !!}
          <a class='btn btn-default dismiss-form' href="javascript:dismissMemberForm()">Fermer</a>
        </div>
      </div>
    </div>
  {!! Form::close() !!}
</div>
