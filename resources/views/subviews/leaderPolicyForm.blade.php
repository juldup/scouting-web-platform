<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
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
?>

<?php
  $leaders = $user->getAssociatedLeaderMembers();
  $signed = true;
  foreach ($leaders as $leader) {
    if (!$leader->leader_policy_signed) $signed = false;
  }
?>

<h2>Signature de la charte</h2>
{{ Form::open(array('class' => 'form-horizontal well', 'url' => URL::route('submit_leader_policy_signature', array('section_slug' => $user->currentSection->slug)))) }}
  @if ($signed)
    <p class='alert alert-info'>Tu as déjà signé la charte des animateurs</p>
  @else
    <p class='alert alert-danger'>Tu n'as pas encore signé la charte des animateurs</p>
  @endif
  <div class='form-group'>
    {{ Form::label('leader_policy_signed', "J'adhère à la charte des animateurs", array("class" => "col-sm-4 control-label")) }}
    <div class="col-sm-1">
      {{ Form::checkbox('leader_policy_signed') }}
    </div>
    <div class="col-sm-7">
      {{ Form::submit('Signer la charte', array('class' => 'btn-primary form-control medium')) }}
    </div>
  </div>
{{ Form::close() }}

<div class="row">
  <div class='col-sm-12'>
    <h2>Liste des signataires</h2>
    @foreach (Member::where('is_leader', '=', true)
                      ->where('validated', '=', true)
                      ->orderBy('leader_name', 'ASC')->get() as $leader)
      <p class='@if ($leader->leader_policy_signed) leader-policy-signed @else leader-policy-unsigned @endif'>
        {{{ $leader->leader_name }}} ({{{ $leader->getFullName()}}}) : 
        @if ($leader->leader_policy_signed)
          Oui
        @else
          Non
        @endif
      </p>
    @endforeach
  </div>
</div>