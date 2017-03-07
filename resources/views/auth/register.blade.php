<!DOCTYPE html>
<html>
<head>
    <title>register</title>
</head>
<body>
    <form method="POST">
        name:<input type="text" name="name" value="{{ old('name') }}" required autofocus /><br />
        email:<input type="text" name="email" value="{{ old('email') }}" required /><br />
        password:<input type="password" name="password" name="password" required /><br />
        password:<input type="password" name="password" name="password_confirmation" required /><br />
        {{ csrf_field() }}
        <input type="submit" value="register" />
    </form>
    <ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    </ul>
</body>
</html>