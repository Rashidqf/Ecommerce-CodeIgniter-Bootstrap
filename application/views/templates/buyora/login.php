<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<main class="buyora-container buyora-public">
    <section class="buyora-public-card buyora-auth-wrap">
        <h1><?= htmlspecialchars(lang('login_to_acc'), ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if ($this->session->flashdata('userError')) { ?>
            <div class="buyora-notice" role="alert"><?= htmlspecialchars($this->session->flashdata('userError'), ENT_QUOTES, 'UTF-8') ?></div>
        <?php } ?>
        <form class="buyora-form" method="POST" action="">
            <label for="user-email"><?= htmlspecialchars(lang('email_address'), ENT_QUOTES, 'UTF-8') ?></label>
            <input id="user-email" type="email" name="email" placeholder="Email" autocomplete="username" required>
            <label for="user-pass"><?= htmlspecialchars(lang('password'), ENT_QUOTES, 'UTF-8') ?></label>
            <input id="user-pass" type="password" name="pass" placeholder="Password" autocomplete="current-password" required>
            <button class="buyora-button buyora-button-primary" type="submit" name="login"><?= htmlspecialchars(lang('login'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
        <nav class="buyora-inline-links" aria-label="Account links">
            <a href="<?= LANG_URL . '/register' ?>"><?= htmlspecialchars(lang('register'), ENT_QUOTES, 'UTF-8') ?></a>
            <a href="<?= LANG_URL . '/vendor/login' ?>">Vendor login</a>
        </nav>
    </section>
</main>
