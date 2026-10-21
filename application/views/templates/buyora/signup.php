<?php defined('BASEPATH') OR exit('No direct script access allowed');
$signupError = $this->session->flashdata('userError');
?>
<main class="buyora-container buyora-public">
    <section class="buyora-public-card buyora-auth-wrap">
        <h1><?= htmlspecialchars(lang('user_register'), ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($signupError)) { ?>
            <div class="buyora-notice" role="alert">
                <?php if (is_array($signupError)) { ?>
                    <ul>
                        <?php foreach ($signupError as $error) { ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php } ?>
                    </ul>
                <?php } else { ?>
                    <?= htmlspecialchars($signupError, ENT_QUOTES, 'UTF-8') ?>
                <?php } ?>
            </div>
        <?php } ?>
        <form class="buyora-form" method="POST" action="">
            <label for="signup-name"><?= htmlspecialchars(lang('name'), ENT_QUOTES, 'UTF-8') ?></label>
            <input id="signup-name" type="text" name="name" placeholder="Name" required>
            <label for="signup-phone"><?= htmlspecialchars(lang('phone'), ENT_QUOTES, 'UTF-8') ?></label>
            <input id="signup-phone" type="text" name="phone" placeholder="Phone" required>
            <label for="signup-email"><?= htmlspecialchars(lang('email_address'), ENT_QUOTES, 'UTF-8') ?></label>
            <input id="signup-email" type="email" name="email" placeholder="Email" autocomplete="email" required>
            <label for="signup-pass"><?= htmlspecialchars(lang('password'), ENT_QUOTES, 'UTF-8') ?></label>
            <input id="signup-pass" type="password" name="pass" placeholder="Password" autocomplete="new-password" required>
            <label for="signup-pass-repeat"><?= htmlspecialchars(lang('password_repeat'), ENT_QUOTES, 'UTF-8') ?></label>
            <input id="signup-pass-repeat" type="password" name="pass_repeat" placeholder="Password repeat" autocomplete="new-password" required>
            <button class="buyora-button buyora-button-primary" type="submit" name="signup"><?= htmlspecialchars(lang('register_me'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
        <nav class="buyora-inline-links" aria-label="Account links">
            <a href="<?= LANG_URL . '/login' ?>"><?= htmlspecialchars(lang('login'), ENT_QUOTES, 'UTF-8') ?></a>
            <a href="<?= LANG_URL . '/vendor/register' ?>" rel="nofollow">Register as a vendor</a>
        </nav>
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 13px; line-height: 1.6; color: #64748b; text-align: left;">
            <strong style="color: #0f172a; display: block; margin-bottom: 6px;">BUYORA Shopper Account Privileges:</strong>
            <ul style="margin: 6px 0 8px 18px; padding: 0;">
                <li>Fast 1-click checkout with multiple saved delivery addresses across Pakistan.</li>
                <li>Exclusive member coupons, flash deal notifications, and seasonal promotions.</li>
                <li>100% verified buyer protection and hassle-free 7-day return claims.</li>
                <li>Live parcel tracking across leading nationwide courier partner networks.</li>
            </ul>
            <p style="margin: 0;">By registering, you gain full access to verified seller ratings, genuine buyer reviews, and guaranteed responsive customer care.</p>
        </div>
    </section>
</main>
