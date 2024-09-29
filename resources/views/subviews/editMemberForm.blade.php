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
use App\Models\Member;

?>

<?php if (!isset($leader_only)) $leader_only = false; ?>
<?php if (!isset($form_legend)) $form_legend = "Membre"; ?>
<?php if (!isset($edit_identity)) $edit_identity = false; ?>
<?php if (!isset($edit_contact)) $edit_contact = true; ?>
<?php if (!isset($edit_section)) $edit_section = false; ?>
<?php if (!isset($edit_totem)) $edit_totem = false; ?>
<?php if (!isset($edit_leader)) $edit_leader = false; ?>
<?php if (!isset($edit_others)) $edit_others = true; ?>
<?php if (!isset($edit_photo)) $edit_photo = false; ?>
<?php if (!isset($form_id)) $form_id = "member_form"; ?>
<?php $can_edit_something = $edit_identity || $edit_contact || $edit_section || $edit_totem || $edit_leader || $edit_others || $edit_photo; ?>

<div id="{!!$form_id !!}" class='well member-form-wrapper'
     @if (!Session::has('_old_input')) style="display: none;" @endif
     >
  <legend>{!!$form_legend !!}</legend>
  {!! Form::open(array('files' => true, 'url' => $submit_url)) !!}
    <div class="form-group">
      <div class="col-md-12">
        <div class="text-center">
          @if ($can_edit_something)
            {!! Form::submit('Enregistrer', array('class' => 'btn btn-primary')) !!}
          @endif
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
            {!! Form::text('first_name', '', array('class' => 'form-control', ($edit_identity ? "enabled" : "disabled") )) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('last_name', "Nom", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('last_name', '', array('class' => 'form-control', ($edit_identity ? "enabled" : "disabled") )) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('birth_date', "Date de naissance", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            {!! Form::text('birth_date_day', '', array('class' => 'small form-control', 'placeholder' => 'Jour', ($edit_identity ? "enabled" : "disabled") )) !!} /
            {!! Form::text('birth_date_month', '', array('class' => 'small form-control', 'placeholder' => 'Mois', ($edit_identity ? "enabled" : "disabled") )) !!} /
            {!! Form::text('birth_date_year', '', array('class' => 'small form-control', 'placeholder' => 'Année', ($edit_identity ? "enabled" : "disabled") )) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('gender', "Sexe", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            {!! Form::select('gender', array('M' => 'Garçon', 'F' => 'Fille'), '', array('class' => 'form-control', ($edit_identity ? "enabled" : "disabled") )) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('nationality', "Nationalité", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('nationality', 'BE', array('class' => 'small form-control', ($edit_identity ? "enabled" : "disabled") )) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('address', "Rue et numéro", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('address', '', array('class' => 'form-control', ($edit_contact ? "enabled" : "disabled"))) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('postcode', "Code postal", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('postcode', '', array('class' => 'form-control', ($edit_contact ? "enabled" : "disabled"))) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('city', "Localité", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('city', '', array('class' => 'form-control', ($edit_contact ? "enabled" : "disabled"))) !!}</div>
        </div>
        @if (!$leader_only)
          <div class="form-group">
            {!! Form::label('phone1', "Téléphone", array('class' => 'control-label col-md-4')) !!}
            <div class='col-md-8'>
              {!! Form::text('phone1', '', array('class' => 'medium form-control', ($edit_contact ? "enabled" : "disabled") )) !!}
              de {!! Form::text('phone1_owner', '', array('class' => 'medium form-control', ($edit_contact ? "enabled" : "disabled") )) !!}
              Confidentiel : {!! Form::checkbox('phone1_private', '1', '', array( ($edit_contact ? "enabled" : "disabled") )) !!}
            </div>
          </div>
          <div class="form-group">
            <div class='col-md-8 col-md-offset-4'>
              {!! Form::text('phone2', '', array('class' => 'medium form-control', ($edit_contact ? "enabled" : "disabled") )) !!}
              de {!! Form::text('phone2_owner', '', array('class' => 'medium form-control', ($edit_contact ? "enabled" : "disabled") )) !!}
              Confidentiel : {!! Form::checkbox('phone2_private', '1', '', array( ($edit_contact ? "enabled" : "disabled") )) !!}
            </div>
          </div>
          <div class="form-group">
            <div class='col-md-8 col-md-offset-4'>
              {!! Form::text('phone3', '', array('class' => 'medium form-control', ($edit_contact ? "enabled" : "disabled") )) !!}
              de {!! Form::text('phone3_owner', '', array('class' => 'medium form-control', ($edit_contact ? "enabled" : "disabled") )) !!}
              Confidentiel : {!! Form::checkbox('phone3_private', '1', '', array( ($edit_contact ? "enabled" : "disabled") )) !!}
            </div>
          </div>
        @endif
        <div class="form-group">
          {!! Form::label('phone_member', ($leader_only ? "GSM" : "GSM du scout"), array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            {!! Form::text('phone_member', '', array('class' => 'form-control medium', ($edit_contact ? "enabled" : "disabled") )) !!}
            Confidentiel : {!! Form::checkbox('phone_member_private', '1', '', array( ($edit_contact ? "enabled" : "disabled") )) !!}
          </div>
        </div>
        @if (!$leader_only)
          <div class="form-group">
            {!! Form::label('email1', "Adresse e-mail", array('class' => 'control-label col-md-4')) !!}
            <div class='col-md-8'>
              {!! Form::text('email1', '', array('placeholder' => "L'adresse e-mail n'est jamais publiée", 'class' => 'form-control', ($edit_contact ? "enabled" : "disabled") )) !!}
            </div>
          </div>
          <div class="form-group">
            <div class='col-md-8 col-md-offset-4'>
              {!! Form::text('email2', '', array('placeholder' => "L'adresse e-mail n'est jamais publiée", 'class' => 'form-control', ($edit_contact ? "enabled" : "disabled") )) !!}
            </div>
          </div>
          <div class="form-group">
            <div class='col-md-8 col-md-offset-4'>
              {!! Form::text('email3', '', array('placeholder' => "L'adresse e-mail n'est jamais publiée", 'class' => 'form-control', ($edit_contact ? "enabled" : "disabled") )) !!}
            </div>
          </div>
        @endif
        <div class="form-group">
          {!! Form::label('email_member', ($leader_only ? "Adresse e-mail" : "Adresse e-mail du scout"), array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('email_member', '', array('placeholder' => "L'adresse e-mail n'est jamais publiée", 'class' => 'form-control', ($edit_contact ? "enabled" : "disabled") )) !!}</div>
        </div>
      </div>
      <div class="col-md-6 form-horizontal">
        <div class="form-group">
          {!! Form::label('section', "Section", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::select('section', Section::getSectionsForSelect(), '', array('class' => 'form-control medium', ($edit_section ? "enabled" : "disabled") )) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('subgroup', isset($subgroup_name) && $subgroup_name ? $subgroup_name : 'Patrouille', array('class' => 'control-label col-md-4')) !!}
          <div class="col-md-8">
            @if ($edit_totem && isset($subgroup_choices) && $subgroup_choices)
              {!! Form::text('subgroup', '', array('class' => 'form-control medium', ($edit_totem ? "enabled" : "disabled") )) !!}
              {!! Form::select('subgroup_select', $subgroup_choices, '', array('class' => 'form-control medium', ($edit_totem ? "enabled" : "disabled") )) !!}
            @else
              {!! Form::text('subgroup', '', array('class' => 'form-control', ($edit_totem ? "enabled" : "disabled") )) !!}
            @endif
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('role', 'Rôle', array('class' => 'control-label col-md-4')) !!}
          <div class="col-md-8">
            @if ($edit_totem && isset($role_choices) && $role_choices)
              {!! Form::text('role', '', array('class' => 'form-control medium', ($edit_totem ? "enabled" : "disabled") )) !!}
              {!! Form::select('role_select', $role_choices, '', array('class' => 'form-control medium', ($edit_totem ? "enabled" : "disabled") )) !!}
            @else
              {!! Form::text('role', '', array('class' => 'form-control', ($edit_totem ? "enabled" : "disabled") )) !!}
            @endif
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('totem', "Totem", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('totem', '', array('class' => 'form-control', ($edit_totem ? "enabled" : "disabled") )) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('quali', "Quali", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('quali', '', array('class' => 'form-control', ($edit_totem ? "enabled" : "disabled") )) !!}</div>
        </div>
        <div class='form-group'>
          {!! Form::label('picture', "Photo", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-1'>
            <img class="edit_listing_picture" src="" alt="/" />
          </div>
          <div class='col-md-7'>
            {!! Form::file('picture', array('class' => 'btn btn-default', $edit_photo ? "enabled" : "disabled")) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('is_guest', "Invité", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            <div class="checkbox">
              {!! Form::checkbox('is_guest', 1, '') !!}
            </div>
          </div>
        </div>
        @if ($leader_only)
          {!! Form::hidden('is_leader', true) !!}
        @else
          <div class="form-group">
            {!! Form::label('is_leader', "Animateur", array('class' => 'control-label col-md-4')) !!}
            <div class='col-md-8'>
              <div class="checkbox">
                {!! Form::checkbox('is_leader', 1, '', array($edit_leader ? "enabled" : "disabled")) !!}
              </div>
            </div>
          </div>
        @endif
        <div class='form-group @if (!$leader_only) leader_specific @endif'>
          {!! Form::label('leader_name', "Nom d'animateur", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::text('leader_name', '', array('placeholder' => "Nom utilisé dans sa section", 'class' => 'form-control', $edit_leader ? "enabled" : "disabled")) !!}</div>
        </div>
        <div class='form-group @if (!$leader_only) leader_specific @endif'>
          {!! Form::label('leader_in_charge', "Animateur responsable", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-2'>
            <div class="checkbox">
              {!! Form::checkbox('leader_in_charge', 1, '', array($edit_leader ? "enabled" : "disabled")) !!}
            </div>
          </div>
          <div class="control-label col-md-4">
            {!! Form::label('list_order', "Numéro") !!}
            <span class="member-order-help"></span> :
          </div>
          <div class="col-md-2">
            {!! Form::text('list_order', '', array('class' => 'form-control')) !!}
          </div>
        </div>
        <div class='form-group @if (!$leader_only) leader_specific @endif'>
          {!! Form::label('leader_description', "Description de l'animateur", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::textarea('leader_description', '', array('placeholder' => "Petite description qui apparaitra sur la page des animateurs", 'class' => 'form-control', 'rows' => 3, $edit_leader ? "enabled" : "disabled")) !!}</div>
        </div>
        <div class='form-group @if (!$leader_only) leader_specific @endif'>
          {!! Form::label('leader_role', "Rôle de l'animateur", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            {!! Form::text('leader_role', '', array('placeholder' => "Rôle particulier dans le staff", 'class' => 'form-control', $edit_leader ? "enabled" : "disabled")) !!}
            <br />
              Afficher le rôle dans la page de contact :
              {!! Form::checkbox('leader_role_in_contact_page', 1, '', array($edit_leader ? "enabled" : "disabled")) !!}
          </div>
        </div>
        <div class="row">
          {!! Form::label('has_handicap', "Handicap", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>
            <div class="checkbox">
              {!! Form::checkbox('has_handicap', 1, '', array($edit_identity ? "enabled" : "disabled")) !!}
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-8 col-md-offset-4">
            {!! Form::textarea('handicap_details', '', array('placeholder' => "Détails du handicap", 'class' => 'form-control', 'rows' => 3, $edit_identity ? "enabled" : "disabled")) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('comments', "Commentaires (privés)", array('class' => 'control-label col-md-4')) !!}
          <div class='col-md-8'>{!! Form::textarea('comments', '', array('placeholder' => 'Toute information utile à partager aux animateurs', 'class' => 'form-control', 'rows' => 3, $edit_others ? "enabled" : "disabled")) !!}</div>
        </div>
        <div class="form-group">
          {!! Form::label('family_in_other_units', "Famille autres unités", array('class' => 'control-label col-md-4', $edit_others ? "enabled" : "disabled")) !!}
          <div class='col-md-8'>
            {!! Form::select('family_in_other_units', Member::getFamilyOtherUnitsForSelect(), '', array('class' => 'form-control', $edit_others ? "enabled" : "disabled")) !!}
            {!! Form::textarea('family_in_other_units_details', '',
                      array('placeholder' => "S'il y a des membres de la même famille dans une autre unité, " .
                                              "cela peut entrainer une réduction de la cotisation. Indiquer " .
                                              "ici qui et dans quelle(s) unité(s).", 'class' => 'form-control', 'rows' => 4, $edit_others ? "enabled" : "disabled")) !!}
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="text-center">
          @if ($can_edit_something)
            {!! Form::submit('Enregistrer', array('class' => 'btn btn-primary')) !!}
          @endif
          <a class='btn btn-default dismiss-form' href="javascript:dismissMemberForm()">Fermer</a>
        </div>
      </div>
    </div>
  {!! Form::close() !!}
</div>
