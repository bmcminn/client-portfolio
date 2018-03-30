<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>Document</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" type="text/css" href="/css/main.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
</head>
<body>
    <!--[if lte IE 9]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->

    <form class="login-form" method="post" action="/auth/login">
        <div class="card">

            <h1 class="form-group text-center">
                Client Login
            </h1>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" autocomplete="current-password">
            </div>

            <div class="form-group text-center">
                <button class="btn btn-primary btn-block" type="submit">Login</button>
            </div>
        </div>

        <div class="form-group px-1">
            <a class="btn btn-link" href="/forgot/password">Forgot password?</a>
        </div>
    </form>

    <script src="/js/helpers.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
    <script src="/js/login.js"></script>
</body>
</html>
