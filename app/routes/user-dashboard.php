<?php



// =========================================================================================================
//  USER/ADMIN DASHBOARD VIEWS
// =========================================================================================================


// ROUTE: user dashboard
$router->get(ROUTES['user_dashboard'], function() use ($db, $twig, $model) {

    $user = $_SESSION['user'];

    $twig->render("{$user['user_type']}-dashboard.twig", $model);

});





// ===========================================================================

// ROUTE: admin dashboard
$router->get(ROUTES['admin_dashboard'], function() use ($db, $twig, $model) {

    $user = $_SESSION['user'];

    // DB call to get clients
    $stmt = $db->query("SELECT * FROM users WHERE user_type='client'");
    $clients = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($users)) {
        $model['clients'] = $clients;
    }

    echo $twig->render('admin-dashboard.twig', $model);

    return;
});


