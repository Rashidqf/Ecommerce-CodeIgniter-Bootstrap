<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<main class="buyora-container buyora-public">
    <section class="buyora-public-card buyora-auth-wrap">
        <h1><?= htmlspecialchars(lang('paypal_success_msg'), ENT_QUOTES, 'UTF-8') ?></h1>
        <a class="buyora-button buyora-button-primary" href="<?= base_url() ?>">Continue shopping</a>
    </section>
</main>
