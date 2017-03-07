<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>login</title>
</head>
<body>
    <form method="POST">
        user name:<input type="text" name="email"><br />
        password :<input type="password" name="password"><br />
        {{ csrf_field() }}
        <input type="submit">
    </form>
    <ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    </ul>
</body>
</html>