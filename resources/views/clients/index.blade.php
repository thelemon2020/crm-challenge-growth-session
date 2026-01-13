<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clients</title>
</head>
<body>
@foreach($clients as $client)
    <b>
        {{$client->name}}
    </b>
@endforeach

</body>
</html>
