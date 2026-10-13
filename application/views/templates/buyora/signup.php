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
            <a href="<?= LANG_URL . '/vendor/register' ?>">Register as a vendor</a>
        </nav>
    </section>
</main>
