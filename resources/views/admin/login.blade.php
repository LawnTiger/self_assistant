<html>
<body>
<form method="POST">
    name:<input name="name"><br>
    password:<input name="password"><br>
    {{ csrf_field() }}
    <input type="submit"><br>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</form>
</body>
</html>
