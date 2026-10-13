<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<main class="buyora-container buyora-public">
    <section class="buyora-panel">
        <h1><?= htmlspecialchars(lang('bank_payment'), ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (isset($_SESSION['order_id'])) { ?>
            <table class="buyora-table">
                <tbody>
                    <tr><th><?= htmlspecialchars(lang('bank_recipient_name'), ENT_QUOTES, 'UTF-8') ?></th><td><?= $bank_account != null ? htmlspecialchars($bank_account['name'], ENT_QUOTES, 'UTF-8') : '' ?></td></tr>
                    <tr><th><?= htmlspecialchars(lang('bank_iban'), ENT_QUOTES, 'UTF-8') ?></th><td><?= $bank_account != null ? htmlspecialchars($bank_account['iban'], ENT_QUOTES, 'UTF-8') : '' ?></td></tr>
                    <tr><th><?= htmlspecialchars(lang('bank_bic'), ENT_QUOTES, 'UTF-8') ?></th><td><?= $bank_account != null ? htmlspecialchars($bank_account['bic'], ENT_QUOTES, 'UTF-8') : '' ?></td></tr>
                    <tr><th><?= htmlspecialchars(lang('bank_name'), ENT_QUOTES, 'UTF-8') ?></th><td><?= $bank_account != null ? htmlspecialchars($bank_account['bank'], ENT_QUOTES, 'UTF-8') : '' ?></td></tr>
                    <tr><th><?= htmlspecialchars(lang('bank_reason'), ENT_QUOTES, 'UTF-8') ?></th><td><?= htmlspecialchars(lang('the_reason'), ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($_SESSION['order_id'], ENT_QUOTES, 'UTF-8') ?></td></tr>
                    <tr><th><?= htmlspecialchars(lang('final_amount_for_pay'), ENT_QUOTES, 'UTF-8') ?></th><td><?= htmlspecialchars($_SESSION['final_amount'], ENT_QUOTES, 'UTF-8') ?></td></tr>
                </tbody>
            </table>
        <?php } ?>
    </section>
</main>
