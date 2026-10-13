<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<main class="buyora-container buyora-public">
    <section class="buyora-public-card buyora-auth-wrap">
        <h1><?= htmlspecialchars(lang('order_confirmed'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="buyora-copy">Your order is confirmed. You can keep shopping or check your account for the order history.</p>
        <a class="buyora-button buyora-button-primary" href="<?= base_url() ?>">Continue shopping</a>
    </section>
</main>
