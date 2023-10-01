@extends('pages.bootstrapping.bootstrapping-base')
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

@section('title')
  Initialisation du site - étape 7
@stop

@section('head')
  <link media="all" type="text/css" rel='stylesheet' href="{{ asset('css/bootstrap-colorpicker.min.css') }}"></link>
@stop

@section('additional_javascript')
  <script src="{{ asset('js/libs/bootstrap-colorpicker.min.js') }}"></script>
  <script>
    $().ready(function() {
      // Add new section
      $('[name="new-section"]').change(function() {
        var value = $(this).val();
        $(this).val('');
        createSection(value);
      });
      // Delete section row
      $('.delete-section-cell').click(function() {
        $(this).closest('.section-row').remove();
      });
      // Color button
      $(".color-sample").click(function(event) {
        var thisColorSample = $(this);
        var inputField = thisColorSample.closest('.section-row').find("[name='color']");
        thisColorSample.colorpicker({
          component: thisColorSample,
          color: $(" [name='section_color']").val()
        }).on('changeColor', function(event) {
          thisColorSample.css('background-color', event.color.toHex());
          inputField.val(event.color.toHex());
        }).on('hidePicker', function(event) {
          thisColorSample.colorpicker('destroy');
        });
        thisColorSample.colorpicker('show');
      });
      $("#submit-sections").click(function(event) {
        submitSections();
      });
    });
    // Add a row for a new section
    function createSection(type) {
      var newElement = $('#section-row-prototype').clone(true);
      $('#section-row-prototype').before(newElement);
      newElement.attr('id', null);
      newElement.removeClass('invisible');
      newElement.find("[name='category']").val(type);
      if (type === 'baladins') {
        newElement.find("[name='name']").val('Baladins');
        newElement.find("[name='color']").val('#000099');
        newElement.find("[name='la_section']").val('la ribambelle');
        newElement.find("[name='de_la_section']").val('de la ribambelle');
        newElement.find("[name='subgroup']").val('Hutte');
        newElement.find("[name='code']").val(codeForType("B", 1));
      } else if (type === 'louveteaux') {
        newElement.find("[name='name']").val('Louveteaux');
        newElement.find("[name='color']").val('#00BB36');
        newElement.find("[name='la_section']").val('la meute');
        newElement.find("[name='de_la_section']").val('de la meute');
        newElement.find("[name='subgroup']").val('Sizaine');
        newElement.find("[name='code']").val(codeForType("L", 1));
      } else if (type === 'eclaireurs') {
        newElement.find("[name='name']").val('Éclaireurs');
        newElement.find("[name='color']").val('#3399FF');
        newElement.find("[name='la_section']").val('la troupe');
        newElement.find("[name='de_la_section']").val('de la troupe');
        newElement.find("[name='subgroup']").val('Patrouille');
        newElement.find("[name='code']").val(codeForType("E", 1));
      } else if (type === 'pionniers') {
        newElement.find("[name='name']").val('Pionniers');
        newElement.find("[name='color']").val('#FF0000');
        newElement.find("[name='la_section']").val('le poste');
        newElement.find("[name='de_la_section']").val('du poste');
        newElement.find("[name='subgroup']").val('Équipe');
        newElement.find("[name='code']").val(codeForType("P", 1));
      } else {
        newElement.find("[name='name']").val(type[0].toUpperCase() + type.substring(1));
        newElement.find("[name='color']").val("#999999");
      }
      newElement.find(".color-sample").css('background-color', newElement.find("[name='color']").val());
    }
    // Compute the next unique code for the given type
    function codeForType(type, index) {
      if (!index) index = 1;
      var found = false;
      $("input[name='code']").each(function() {
        if ($(this).val() === type + index) found = true;
      });
      if (found) return codeForType(type, index + 1);
      return type + index;
    }
    // Submit section list
    function submitSections() {
      var sectionData = [];
      $(".section-row:not(#section-row-prototype)").each(function() {
        var data = {
          name: $(this).find("[name='name']").val(),
          email: $(this).find("[name='email']").val(),
          category: $(this).find("[name='category']").val(),
          code: $(this).find("[name='code']").val(),
          color: $(this).find("[name='color']").val(),
          la_section: $(this).find("[name='la_section']").val(),
          de_la_section: $(this).find("[name='de_la_section']").val(),
          subgroup: $(this).find("[name='subgroup']").val()
        };
        sectionData.push(data);
      });
      var form = document.createElement("form");
      form.setAttribute("method", "post");
      var hiddenField = document.createElement("input");
      hiddenField.setAttribute("type", "hidden");
      hiddenField.setAttribute("name", "data");
      hiddenField.setAttribute("value", JSON.stringify(sectionData));
      form.appendChild(hiddenField);
      document.body.appendChild(form);
      form.submit();
    }
  </script>
@stop

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <h1>Étape 7 : Créer les sections</h1>
      @if ($error_message)
      <p class="alert alert-danger">{{ $error_message }}</p>
      @endif
      <table class="table table-bordered" id='section-table'>
        <thead>
          <tr>
            <th>Nom</th>
            <th>E-mail</th>
            <th>Sigle</th>
            <th>Couleur</th>
            <th>"la section"</th>
            <th>"de la section"</th>
            <th>Sous-groupes</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr id="section-row-prototype" class="section-row invisible">
            <td><input type='text' class="invisible-input" name="name"></td>
            <td><input type='text' class="invisible-input" name="email"></td>
            <td>
              <input type="hidden" name="category">
              <input type='text' class="invisible-input small" name="code">
            </td>
            <td>
              <input type='hidden' name="color">
              <a class="color-sample"></a>
            </td>
            <td><input type='text' class="invisible-input" name="la_section"></td>
            <td><input type='text' class="invisible-input" name="de_la_section"></td>
            <td><input type='text' class="invisible-input" name="subgroup"></td>
            <td class='delete-section-cell'><span class="glyphicon glyphicon-remove"></span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-4"> 
      {{ Form::select('new-section', array_merge(array("" => "Ajouter une section de type..."), Section::categoriesForSelect()), '', array('class' => "form-control large")) }}
    </div>
    <div class="col-sm-4">
      <button id="submit-sections" class="btn btn-primary">Valider les sections</button>
    </div>
  </div>  
@stop
