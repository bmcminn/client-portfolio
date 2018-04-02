<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Document</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" href="favicon.png">
    <link rel="manifest" href="site.webmanifest">
    <link rel="stylesheet" href="/css/main.css">

    <?php if (env('APP_DEBUG')) : ?>
        <script src="/js/modernizr.min.js"></script>
    <?php else : ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <?php endif; ?>

</head>
<body>



    <?php if (env('APP_DEBUG')) : ?>
        <script src="/js/axios.min.js"></script>
        <script src="/js/vue.min.js"></script>
        <script src="/js/vuex.min.js"></script>
        <script src="/js/vue-router.min.js"></script>
    <?php else : ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.16/vue.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vuex/3.0.1/vuex.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-router/3.0.1/vue-router.min.js"></script>
    <?php endif; ?>

    <script src="/js/helpers.js"></script>
    <script src="/js/main.js"></script>
</body>
</html>
