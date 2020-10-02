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

{
  'first_name': "{{ Helper::sanitizeForJavascript($member->first_name) }}",
  'last_name': "{{ Helper::sanitizeForJavascript($member->last_name) }}",
  'birth_date_day': "{{ Helper::getDateDay($member->birth_date) }}",
  'birth_date_month': "{{ Helper::getDateMonth($member->birth_date) }}",
  'birth_date_year': "{{ Helper::getDateYear($member->birth_date) }}",
  'gender': "{{{ $member->gender }}}",
  'nationality': "{{{ $member->nationality }}}",
  'address': "{{ Helper::sanitizeForJavascript($member->address) }}",
  'postcode': "{{ Helper::sanitizeForJavascript($member->postcode) }}",
  'city': "{{ Helper::sanitizeForJavascript($member->city) }}",
  'has_handicap': {{ $member->has_handicap ? "true" : "false" }},
  'handicap_details': "{{ Helper::sanitizeForJavascript($member->handicap_details) }}",
  'comments': "{{ Helper::sanitizeForJavascript($member->comments) }}",
  'leader_name': "{{ Helper::sanitizeForJavascript($member->leader_name) }}",
  'leader_in_charge': {{ $member->leader_in_charge ? "true" : "false" }},
  'list_order': {{ $member->list_order }},
  'leader_description': "{{ Helper::sanitizeForJavascript($member->leader_description) }}",
  'leader_role': "{{ Helper::sanitizeForJavascript($member->leader_role) }}",
  'section_id': {{ $member->section_id }},
  'subgroup': "{{ Helper::sanitizeForJavascript($member->subgroup) }}",
  'role': "{{ Helper::sanitizeForJavascript($member->role) }}",
  'phone1': "{{ Helper::sanitizeForJavascript($member->phone1) }}",
  'phone1_owner': "{{ Helper::sanitizeForJavascript($member->phone1_owner); }}",
  'phone1_private': {{ $member->phone1_private ? "true" : "false" }},
  'phone2': "{{ Helper::sanitizeForJavascript($member->phone2) }}",
  'phone2_owner': "{{ Helper::sanitizeForJavascript($member->phone2_owner); }}",
  'phone2_private': {{ $member->phone2_private ? "true" : "false" }},
  'phone3': "{{ Helper::sanitizeForJavascript($member->phone3) }}",
  'phone3_owner': "{{ Helper::sanitizeForJavascript($member->phone3_owner); }}",
  'phone3_private': {{ $member->phone3_private ? "true" : "false" }},
  'phone_member': "{{ Helper::sanitizeForJavascript($member->phone_member) }}",
  'phone_member_private': {{ $member->phone_member_private ? "true" : "false" }},
  'email1': "{{ Helper::sanitizeForJavascript($member->email1) }}",
  'email2': "{{ Helper::sanitizeForJavascript($member->email2) }}",
  'email3': "{{ Helper::sanitizeForJavascript($member->email3) }}",
  'email_member': "{{ Helper::sanitizeForJavascript($member->email_member) }}",
  'totem': "{{ Helper::sanitizeForJavascript($member->totem) }}",
  'quali': "{{ Helper::sanitizeForJavascript($member->quali) }}",
  'family_in_other_units': {{{ $member->family_in_other_units ? $member->family_in_other_units : 0 }}},
  'family_in_other_units_details' : "{{ Helper::sanitizeForJavascript($member->family_in_other_units_details) }}",
  'has_picture': {{ $member->has_picture ? "true" : "false" }},
  'picture_url': "{{ $member->has_picture ? $member->getPictureURL() : "" }}"
}