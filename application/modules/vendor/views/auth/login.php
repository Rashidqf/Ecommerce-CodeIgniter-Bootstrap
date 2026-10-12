<main class="buyora-auth-stage">
    <section class="buyora-auth-card" aria-labelledby="buyora-vendor-login-title">
        <?php if ($this->session->flashdata('login_error')) { ?>
            <div class="buyora-auth-alert" role="alert"><?= htmlspecialchars($this->session->flashdata('login_error'), ENT_QUOTES, 'UTF-8') ?></div>
        <?php } ?>
        <img class="buyora-auth-brand" src="<?= base_url('assets/buyora/buyora-logo.png') ?>" alt="BUYORA">
        <h1 id="buyora-vendor-login-title"><?= htmlspecialchars(lang('login_to_your_acc'), ENT_QUOTES, 'UTF-8') ?></h1>
        <form method="POST" action="">
            <div class="buyora-auth-field">
                <label for="vendor-email"><?= htmlspecialchars(lang('email'), ENT_QUOTES, 'UTF-8') ?></label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                    <input id="vendor-email" type="email" name="u_email" placeholder="<?= htmlspecialchars(lang('email'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="username" required>
                </div>
            </div>
            <div class="buyora-auth-field">
                <label for="vendor-password"><?= htmlspecialchars(lang('password'), ENT_QUOTES, 'UTF-8') ?></label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input id="vendor-password" type="password" name="u_password" placeholder="<?= htmlspecialchars(lang('password'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="current-password" required>
                </div>
            </div>
            <div class="buyora-auth-options">
                <label><input type="checkbox" name="remember_me"> <?= htmlspecialchars(lang('remember_me'), ENT_QUOTES, 'UTF-8') ?></label>
                <a href="<?= LANG_URL . '/vendor/forgotten-password' ?>"><?= htmlspecialchars(lang('forgot_pass'), ENT_QUOTES, 'UTF-8') ?></a>
            </div>
            <button type="submit" name="login" class="buyora-auth-submit"><?= htmlspecialchars(lang('u_login'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
        <nav class="buyora-auth-links" aria-label="Account links">
            <a href="<?= LANG_URL . '/vendor/register' ?>"><?= htmlspecialchars(lang('register_me'), ENT_QUOTES, 'UTF-8') ?></a>
            <a href="<?= base_url('admin') ?>">Administrator Login</a>
        </nav>
        <p class="buyora-auth-security"><i class="fa fa-lock" aria-hidden="true"></i>Authorized personnel only. Log out securely.</p>
    </section>
</main>