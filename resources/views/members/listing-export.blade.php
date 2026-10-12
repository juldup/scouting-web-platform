<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Listing</title>
  <style>
    body {
      font-family: "Source Sans Pro", "Arial", sans-serif;
      font-size: 85%;
    }
    
    table {
      width: 100%;
      max-width: 100%;
    }
    
    table, tr, th, td {
      border-width: 0;
      border-style: solid;
      border-color: #aaaaaa;
      border-collapse: collapse;
    }
    
    tr {
      border-bottom-width: 1px;
    }
    
    th, td {
      padding: .5rem .5rem;
    }
    
    th.rows-separator {
      padding-top: 1rem;
      font-size: 1.1rem;
      font-style: italic;
    }
    
    th { 
      text-align: left;
    }
    
    .page-break {
      page-break-after: always;
    }

    @page {
      size: landscape A4;
    }
  </style>
</head>
<body>

@foreach ($data->tables as $key => $rows)
  <h1>Listing {{ $data->sections[$key]->de_la_section }}</h1>
  <table>
    @if (!empty($rows['scouts']))
      <tr>
        @if (!$data->groupBySection)
          <th class="rows-separator" colspan="9">Scouts</th>
        @else
          <th class="rows-separator" colspan="8">Scouts</th>
        @endif
      </tr>
    
      <tr>
        @if (!$data->groupBySection)
        <th style="width: 5rem;">Section</th>
        @endif
        <th style="width: 8rem;">Nom</th>
        <th style="width: 8rem;">Prénom</th>
        <th style="width: 2rem;">Sexe</th>
        <th style="width: 3rem;">DDN</th>
        <th>Adresse</th>
        <th style="width: 2rem;">CP</th>
        <th style="width: 14rem;">Localité</th>
        <th style="width: 8rem;">Téléphone</th>
      </tr>
    @endif
    
    @foreach ($rows['scouts'] as $row)
      @if ($row["Fonction"] == 'Scout')
      <tr>
        @if (!$data->groupBySection)
        <td>{{ $row["Section"] }}</td>
        @endif
        <td>{{ $row["Nom"] }}</td>
        <td>{{ $row["Prénom"] }}</td>
        <td>{{ $row["Sexe"] }}</td>
        <td>{{ $row["DDN"] }}</td>
        <td>{{ $row["Adresse"] }}</td>
        <td>{{ $row["CP"] }}</td>
        <td>{{ $row["Localité"] }}</td>
        <td>{{ $row["Téléphone"] }}</td>
      </tr>
      @endif
    @endforeach

    @if (!empty($rows['leaders']))
      <tr>
        @if (!$data->groupBySection)
          <th class="rows-separator" colspan="9">Animateurs</th>
        @else
          <th class="rows-separator" colspan="8">Animateurs</th>
        @endif
      </tr>

      <tr>
        @if (!$data->groupBySection)
          <th style="width: 5rem;">Section</th>
        @endif
        <th style="width: 8rem;">Nom</th>
        <th style="width: 8rem;">Prénom</th>
        <th style="width: 2rem;">Sexe</th>
        <th style="width: 3rem;">DDN</th>
        <th>Adresse</th>
        <th style="width: 2rem;">CP</th>
        <th style="width: 14rem;">Localité</th>
        <th style="width: 8rem;">Téléphone</th>
      </tr>

      @foreach ($rows['leaders'] as $row)
        @if ($row["Fonction"] != 'Scout')
          <tr>
            @if (!$data->groupBySection)
              <td>{{ $row["Section"] }}</td>
            @endif
            <td>{{ $row["Nom"] }}</td>
            <td>{{ $row["Prénom"] }}</td>
            <td>{{ $row["Sexe"] }}</td>
            <td>{{ $row["DDN"] }}</td>
            <td>{{ $row["Adresse"] }}</td>
            <td>{{ $row["CP"] }}</td>
            <td>{{ $row["Localité"] }}</td>
            <td>{{ $row["Téléphone"] }}</td>
          </tr>
        @endif
      @endforeach
    @endif
  </table>
  
  @if (!$loop->last)
    <div class="page-break"></div>
  @endif
@endforeach

</body>
</html>
