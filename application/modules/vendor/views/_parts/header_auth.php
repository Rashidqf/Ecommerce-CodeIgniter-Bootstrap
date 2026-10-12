<!DOCTYPE html>
<html lang="<?= MY_LANGUAGE_ABBR ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?= $description ?>">
        <title><?= $title ?></title>
        <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?= base_url('assets/bootstrap-select-1.12.1/bootstrap-select.min.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/vendors.css') ?>">
        <link rel="stylesheet" href="<?= base_url('assets/buyora/buyora-auth.css') ?>">
        <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="buyora-auth-page">
        <header class="buyora-auth-topbar">
            <span><i class="fa fa-shopping-bag" aria-hidden="true"></i> BuyOra Admin/Vendor Access</span>
            <a href="#auth-help"><i class="fa fa-question-circle" aria-hidden="true"></i> Help</a>
        </header>
        <div id="wrapper">
            <div id="content">
