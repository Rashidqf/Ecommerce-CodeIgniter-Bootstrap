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
            <a href="<?= LANG_URL . '/vendor/login' ?>" rel="nofollow">Vendor login</a>
        </nav>
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 13px; line-height: 1.6; color: #64748b; text-align: left;">
            <strong style="color: #0f172a; display: block; margin-bottom: 6px;">Why Sign In to Your BUYORA Account?</strong>
            <p style="margin: 0 0 8px 0;">Sign in to quickly access your saved cart items, manage multiple delivery addresses, track courier dispatches in real-time, and download official purchase invoices with ease.</p>
            <p style="margin: 0;">Your personal credentials and payment details are safeguarded by 256-bit SSL encryption and strict privacy standards. BUYORA never shares your details with unverified third parties.</p>
        </div>
    </section>
</main>
