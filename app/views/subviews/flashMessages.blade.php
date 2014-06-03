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

@if (Session::has('success_message'))
  <p class='alert alert-success'>{{ Session::get('success_message'); }}</p>
@endif
@if (Session::has('error_message'))
  <p class='alert alert-danger'>{{ Session::get('error_message'); }}</p>
@endif
@if (Session::has('warning_message'))
  <p class='alert alert-warning'>{{ Session::get('warning_message'); }}</p>
@endif
