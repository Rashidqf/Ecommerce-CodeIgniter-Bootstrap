<main class="buyora-auth-stage">
    <section class="buyora-auth-card" aria-labelledby="buyora-vendor-recover-title">
        <img class="buyora-auth-brand" src="<?= base_url('assets/buyora/buyora-logo.png') ?>" alt="BUYORA">
        <h1 id="buyora-vendor-recover-title"><?= htmlspecialchars(lang('user_forgotten_page'), ENT_QUOTES, 'UTF-8') ?></h1>
        <form method="POST" action="">
            <div class="buyora-auth-field">
                <label for="vendor-recover-email"><?= htmlspecialchars(lang('email'), ENT_QUOTES, 'UTF-8') ?></label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                    <input id="vendor-recover-email" type="email" name="u_email" placeholder="<?= htmlspecialchars(lang('email'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required>
                </div>
            </div>
            <button type="submit" name="login" class="buyora-auth-submit"><?= htmlspecialchars(lang('send_me_new_pass'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
        <nav class="buyora-auth-links" aria-label="Account links">
            <a href="<?= LANG_URL . '/vendor/login' ?>">Return to Vendor Login</a>
        </nav>
        <p class="buyora-auth-security"><i class="fa fa-lock" aria-hidden="true"></i>We'll send recovery instructions to your registered email.</p>
    </section>
</main>