<!DOCTYPE html>
<html>
<head>
    <title>self assistant</title>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/materialadmin6.min.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/admin.min.css">
</head>
<body class="menubar-hoverable header-fixed">
<section class="section-account">
    <div class="spacer">
        <div class="logo">
            {{--<img src="/images/logo.png">--}}
        </div>
    </div>
    <div class="card contain-sm style-transparent">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <span class="text-lg text-bold text-primary">self assistant</span>
                    <form class="form" action="{{ action('Admin\LoginController@postIndex') }}" method="post">
                        <div class="form-group">
                            <input type="text" class="form-control" id="username" name="name">
                            <label for="username">用户名</label>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password">
                            <label for="password">密码</label>
                        </div>
                        {{ csrf_field() }}
                        @if ($errors->any())
                            <div class="alert alert-callout alert-danger">{{ $errors->first() }}</div>
                        @endif
                        <div class="row">
                            <div class="col-xs-6 text-left">
                                <div class="checkbox checkbox-inline checkbox-styled">
                                    {{--<label>
                                        <input type="checkbox" name="remember">
                                        <span>记住我</span>
                                    </label>--}}
                                </div>
                            </div>
                            <div class="col-xs-6 text-right">
                                <button class="btn btn-primary btn-raised" type="submit">登录</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>
    </div>
</section>
<script src="/assets/js/ganguo-admin.min.js"></script>
</body>
</html>

