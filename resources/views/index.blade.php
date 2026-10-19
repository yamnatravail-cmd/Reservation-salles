<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients List</title>
</head>
<body>

<h1>Clients</h1>
<ul>
    @foreach ($clients as $client)
        <li>{{ $client->nom }} {{ $client->Prenom }} - {{ $client->email }}</li>
    @endforeach
</ul>

</body>
</html>
