<!DOCTYPE html>
<html>
<head>
    <title>共事 - 管理后台</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/materialadmin6.min.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/material-design-iconic-font.min.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/admin.css">
    <link type="text/css" rel="stylesheet" href="/assets/css/sweetalert.css">
    @yield('style')
</head>
<body class="menubar-hoverable header-fixed menubar-pin">
<header id="header">
    <div class="headerbar">
        <div class="headerbar-left">
            <ul class="header-nav header-nav-options">
                <li class="header-nav-brand" >
                    <div class="brand-holder">
                        <a href="">
                            <span class="text-lg text-bold text-primary">self assitance</span>
                        </a>
                    </div>
                </li>
                <li>
                    <a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                        <i class="fa fa-bars"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="headerbar-right">
            <ul class="header-nav header-nav-profile">
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">
                                <span class="profile-info">
                                    <br/>
                                    <span class="text-lg text-bold text-primary">{{ Auth::guard('admin')->user()->name }}</span>
                                </span>
                    </a>
                    <ul class="dropdown-menu animation-dock">
                        <li class="dropdown-header">设置</li>
                        <li>
                            @if (Auth::guard('admin')->user()->role == 'service')
                                <a href="#" name="edit"><i class="fa fa-fw fa-user-secret text-danger"></i>
                                    修改状态</a>
                            @endif
                        </li>
                        <li>
                            <a href="{{ action('Admin\LoginController@out') }}">
                                <i class="fa fa-fw fa-power-off text-danger"></i>退出登录
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</header>
<div id="base">
    <div id="content">
        <section>
            <div class="section-body">
                @yield('content')
            </div>
        </section>
    </div>
    <div id="menubar" class="menubar-inverse">
        <div class="menubar-fixed-panel">
            <div>
                <a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
        </div>
        <div class="menubar-scroll-panel">
            <ul id="main-menu" class="gui-controls">
                <li class="gui-folder">
                    <a>
                        <div class="gui-icon"><i class="md md-account-child"></i></div>
                        <span class="title">用户管理</span>
                    </a>
                    {{--<ul>
                        <li>
                            <a href="{{ action('') }}">
                                <span class="title">技术人员管理</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ action('') }}">
                                <span class="title">用人单位管理</span>
                            </a>
                        </li>
                    </ul>--}}
                </li>
                <li class="gui-folder">
                    <a>
                        <div class="gui-icon"><i class="md md-list"></i></div>
                        <span class="title">笔记管理</span>
                    </a>
                </li>
                <li class="gui-folder">
                    <a href="{{--{{ action('') }}--}}">
                        <div class="gui-icon"><i class="md md-people"></i></div>
                        <span class="title">socket口管理</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<script src="/assets/js/sweetalert.js"></script>
<script src="/assets/js/ganguo-admin.min.js"></script>
@yield('script')
</body>
</html>
