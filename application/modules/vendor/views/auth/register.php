<main class="buyora-auth-stage">
    <section class="buyora-auth-card" aria-labelledby="buyora-vendor-register-title">
        <?php $registerErrors = $this->session->flashdata('error_register'); ?>
        <?php if (is_array($registerErrors) && $registerErrors) { ?>
            <div class="buyora-auth-alert" role="alert">
                <ul>
                    <?php foreach ($registerErrors as $registerError) { ?>
                        <li><?= htmlspecialchars($registerError, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <img class="buyora-auth-brand" src="<?= base_url('assets/buyora/buyora-logo.png') ?>" alt="BUYORA">
        <h1 id="buyora-vendor-register-title"><?= htmlspecialchars(lang('user_register_page'), ENT_QUOTES, 'UTF-8') ?></h1>
        <form method="POST" action="">
            <div class="buyora-auth-field">
                <label for="vendor-register-email"><?= htmlspecialchars(lang('email'), ENT_QUOTES, 'UTF-8') ?></label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                    <input id="vendor-register-email" type="email" name="u_email" value="<?= htmlspecialchars((string) $this->session->flashdata('email'), ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars(lang('email'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required>
                </div>
            </div>
            <div class="buyora-auth-field">
                <label for="vendor-register-password"><?= htmlspecialchars(lang('password'), ENT_QUOTES, 'UTF-8') ?></label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input id="vendor-register-password" type="password" name="u_password" placeholder="<?= htmlspecialchars(lang('password'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="new-password" required>
                </div>
            </div>
            <div class="buyora-auth-field">
                <label for="vendor-register-password-repeat"><?= htmlspecialchars(lang('password_repeat'), ENT_QUOTES, 'UTF-8') ?></label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input id="vendor-register-password-repeat" type="password" name="u_password_repeat" placeholder="<?= htmlspecialchars(lang('password_repeat'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="new-password" required>
                </div>
            </div>
            <button type="submit" name="register" class="buyora-auth-submit"><?= htmlspecialchars(lang('register_me'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
        <nav class="buyora-auth-links" aria-label="Account links">
            <a href="<?= LANG_URL . '/vendor/login' ?>">Already registered? Sign in</a>
            <a href="<?= base_url('admin') ?>">Administrator Login</a>
        </nav>
        <p class="buyora-auth-security"><i class="fa fa-lock" aria-hidden="true"></i>Your vendor account may require administrator approval.</p>
    </section>
</main>