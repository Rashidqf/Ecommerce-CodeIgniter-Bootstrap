<main class="buyora-auth-stage">
    <section class="buyora-auth-card" aria-labelledby="buyora-admin-login-title">
        <?php if ($this->session->flashdata('err_login')) { ?>
            <div id="output" class="buyora-auth-alert" role="alert"><?= htmlspecialchars($this->session->flashdata('err_login'), ENT_QUOTES, 'UTF-8') ?></div>
        <?php } ?>
        <img class="buyora-auth-brand" src="<?= base_url('assets/buyora/buyora-logo.png') ?>" alt="BUYORA">
        <h1 id="buyora-admin-login-title">Welcome, Administrator or Vendor. Please Login.</h1>
        <form action="" method="POST">
            <div class="buyora-auth-field">
                <label for="admin-username">Username / Email</label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                    <input id="admin-username" type="text" name="username" placeholder="Enter your username" autocomplete="username" required>
                </div>
            </div>
            <div class="buyora-auth-field">
                <label for="admin-password">Password</label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input id="admin-password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                </div>
            </div>
            <button class="buyora-auth-submit" type="submit">Login to Dashboard</button>
        </form>
        <nav class="buyora-auth-links" aria-label="Account links">
            <a href="<?= base_url('vendor/register') ?>">Register as a New Vendor</a>
            <a href="<?= base_url('vendor/login') ?>">Vendor Login</a>
        </nav>
        <p class="buyora-auth-security"><i class="fa fa-lock" aria-hidden="true"></i>Authorized personnel only. Log out securely.</p>
    </section>
</main>