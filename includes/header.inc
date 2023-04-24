<?php

// This sanitizes all input for security
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Check Public access
if (isset($_GET['public']) && (CONFIG_SOPLANNING_OPTION_ACCES == 1 || CONFIG_SOPLANNING_OPTION_ACCES == 2)) {
    $_SESSION['public']=1;
    $_SESSION['user_id']='publicspl';
    switch (CONFIG_SOPLANNING_OPTION_ACCES) {
    // Public access
    case 1:
        break;
    // Public access with key
    case 2:
        if ($_GET['cle'] != CONFIG_SECURE_KEY) {
            unset($_SESSION['public']);
            unset($_SESSION['user_id']);
            break;
        }
    // other
    // no break
    default:
        unset($_SESSION['public']);
        unset($_SESSION['user_id']);
    }
}

// Connexion if not connect (with session value)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
    // direct authentification check
    if (isset($_COOKIE['direct_auth']) && $_COOKIE['direct_auth'] != '') {
        $data = explode(';', $_COOKIE['direct_auth']);
        // Check cookies fields
        if (count($data) != 3) {
            setcookie('direct_auth', '', time() - 3600, '/');
            header(HOME_REDIRECTION);
            exit;
        }
        // Check if user exist
        $user_id = trim($data[0]);
        $user = new User();
        if (!$user->db_load(array('user_id', '=', $user_id, 'login_actif', '=', 'oui'))) {
            setcookie('direct_auth', '', time() - 3600, '/');
            header(HOME_REDIRECTION);
            exit;
        }
        // Check password cookie
        $check = sha1($user->user_id . $data[1] . $user->cle);
        if ($check != $data[2]) {
            setcookie('direct_auth', '', time() - 3600, '/');
            header(HOME_REDIRECTION);
            exit;
        }
        // Check date modif
        if (!is_null($user->date_modif) && $user->date_modif > $data[1]) {
            setcookie('direct_auth', '', time() - 3600, '/');
            header(HOME_REDIRECTION);
            exit;
        }
        // Write cookie
        $now = new Datetime();
        $dateCheck = DateTime::createFromFormat('Y-m-d H:i:s', $data[1]);
        $dateCheck->modify('+45 days');
        if ($dateCheck < $now) {
            setcookie('direct_auth', '', time() - 3600, '/');
            header(HOME_REDIRECTION);
            exit;
        }

        // in order to avoid 2fa check if activated
        $_SESSION['2fa_ok'] = 'ok';

        // User init session
        $user->initPostLogin();
    } else {
        $_SESSION['message'] = 'erreur_fin_connexion';
        header(HOME_REDIRECTION);
        exit;
    }
}

// Check Google 2FA
if (CONFIG_GOOGLE_2FA_ACTIVE == '1' && !isset($_SESSION['2fa_ok']) && basename($_SERVER['REQUEST_URI']) != '2fa.php') {
    header('Location: ' . BASE . '/2fa.php');
    exit;
}

// User verification (current connexion)
$user = new User();
if (!$user->db_load(array('user_id', '=', $_SESSION['user_id']))) {
    $_SESSION['message'] = 'erreur_fin_connexion';
    header(HOME_REDIRECTION);
    exit;
}

// Smarty starting
 if (!isset($smarty)) {
     $smarty = new MySmarty();
 }
// Smarty Assign User Data
$userdata=$user->getSmartyData();
$smarty->assign('user', $userdata);

// Smarty assign Version Info
$version = new Version();
$infoVersion = $version->getVersion();
$smarty->assign('infoVersion', $infoVersion);

// Smarty Help URL
$tabAide = array('planning.php' => 'planning.php', 'ressources.php' => 'ressources.php', 'lieux.php' => 'lieux.php', 'user_list.php' => 'utilisateurs.php', 'user_groupes.php' => 'equipes.php', 'projets.php' => 'projets.php', 'groupe_list.php' => 'groupes.php');
$urlCourante = $_SERVER['REQUEST_URI'];    
if (strpos($urlCourante, '/aide/') !== false) {
   $lienAide = 'index.php';
} else {
  foreach ($tabAide as $pageCourante => $urlAide) {
  if (strpos($urlCourante, $pageCourante) !== false) {
     $lienAide = $urlAide;
    }
  }
  if (!isset($lienAide)) {
    $lienAide = 'index.php';
  }
}
$smarty->assign('lienAide', BASE . '/aide/' . $lienAide);