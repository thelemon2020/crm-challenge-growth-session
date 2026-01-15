<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client</title>
</head>
<body>
<h1>{{$client->name}}</h1>
@foreach($projects as $project)
    <div>
        <p>{{$project->name}}</p>
        <p>{{$project->description}}</p>
        <p>{{$project->deadline}}</p>
        <p>{{$project->status}}</p>
    </div>
@endforeach

</body>
</html>
