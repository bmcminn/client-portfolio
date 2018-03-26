<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>

    <link rel="stylesheet" type="text/css" href="/css/main.css">
</head>
<body>

    <form method="post" action="/auth/login">
        <div class="form-group">
            <label for="useremail">Email</label>
            <input type="email" name="useremail" id="useremail">
        </div>

        <div class="form-group">
            <label for="userpassword">Password</label>
            <input type="password" name="userpassword" id="userpassword">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Login</button>
            <a class="btn btn-link" href="/forgot/password">Forgot password?</a>
        </div>
    </form>

</body>
</html>
