<main class="buyora-auth-stage">
    <section class="buyora-auth-card" aria-labelledby="buyora-vendor-register-title" style="max-width: 520px;">
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
        <p style="color: #64748b; font-size: 14px; text-align: center; margin-top: -10px; margin-bottom: 20px;">Register your store and start selling on BUYORA Marketplace</p>

        <form method="POST" action="">
            <div class="buyora-auth-field">
                <label for="vendor-register-name">Store / Business Name</label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                    <input id="vendor-register-name" type="text" name="name"
                        value="<?= htmlspecialchars((string) (isset($_POST['name']) ? $_POST['name'] : ''), ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="e.g. Bio Lab Traders" autocomplete="organization">
                </div>
            </div>
            <div class="buyora-auth-field">
                <label for="vendor-register-email"><?= htmlspecialchars(lang('email'), ENT_QUOTES, 'UTF-8') ?> *</label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                    <input id="vendor-register-email" type="email" name="u_email"
                        value="<?= htmlspecialchars((string) $this->session->flashdata('email'), ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="<?= htmlspecialchars(lang('email'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="email"
                        required>
                </div>
            </div>
            <div class="buyora-auth-field">
                <label for="vendor-register-password"><?= htmlspecialchars(lang('password'), ENT_QUOTES, 'UTF-8') ?> *</label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input id="vendor-register-password" type="password" name="u_password"
                        placeholder="<?= htmlspecialchars(lang('password'), ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="new-password" required>
                </div>
            </div>
            <div class="buyora-auth-field">
                <label for="vendor-register-password-repeat"><?= htmlspecialchars(lang('password_repeat'), ENT_QUOTES, 'UTF-8') ?> *</label>
                <div class="buyora-auth-input-wrap">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input id="vendor-register-password-repeat" type="password" name="u_password_repeat"
                        placeholder="<?= htmlspecialchars(lang('password_repeat'), ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="new-password" required>
                </div>
            </div>

            <!-- Optional Payout Details -->
            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                <p style="margin: 0 0 10px 0; font-size: 12px; font-weight: 600; color: #475569;">
                    <i class="fa fa-university text-danger"></i> Payout Bank Account (Optional - can be added later)
                </p>
                <div class="buyora-auth-field" style="margin-bottom: 10px;">
                    <div class="buyora-auth-input-wrap">
                        <i class="fa fa-university" aria-hidden="true"></i>
                        <input type="text" name="bank_name" placeholder="Bank Name (e.g. Bank Alfalah, Meezan Bank)">
                    </div>
                </div>
                <div class="buyora-auth-field" style="margin-bottom: 5px;">
                    <div class="buyora-auth-input-wrap">
                        <i class="fa fa-credit-card" aria-hidden="true"></i>
                        <input type="text" name="iban" placeholder="IBAN (e.g. PK36ALFH000...)" style="text-transform: uppercase;">
                    </div>
                </div>
            </div>

            <button type="submit" name="register"
                class="buyora-auth-submit"><?= htmlspecialchars(lang('register_me'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
        <nav class="buyora-auth-links" aria-label="Account links">
            <a href="<?= LANG_URL . '/vendor/login' ?>">Already registered? Sign in</a>
            <a href="<?= base_url('admin') ?>">Administrator Login</a>
        </nav>
    </section>
</main>
