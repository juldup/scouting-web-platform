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

Vous venez de créer un compte d'utilisateur sur le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }} : {{ $username }}.

Pour avoir accès aux données confidentielles du site, vous devez activer votre compte en cliquant sur ce lien :
{{ URL::route('verify_user', array("code" => $verification_code)) }}


Si vous n'avez pas créé vous-même ce compte d'utilisateur, veuillez ignorer ce message.

Cordialement,
Le gestionnaire du site
@stop