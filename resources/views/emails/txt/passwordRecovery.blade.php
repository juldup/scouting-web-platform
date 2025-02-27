@extends('emails.txt.emailBase')
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
use App\Models\MemberHistory;

?>

@section('body')
Bonjour,

Vous avez demandé à récupérer votre mot de passe sur le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}.

@if (count($recoveries) == 1)
Cliquez sur le lien suivant pour choisir un nouveau mot de passe pour votre compte {{ key($recoveries) }} :
{{ URL::route('change_password', array("code" => reset($recoveries)->code)) }}
@else
Vous possédez plusieurs comptes d'utilisateur. Voici les liens pour changer leurs mots de passe :

@foreach ($recoveries as $username=>$recovery)
- {{ $username }} : {{ URL::route('change_password', array("code" => $recovery->code)) }} 
@endforeach
@endif


Cordialement,
Le webmaster
@stop
