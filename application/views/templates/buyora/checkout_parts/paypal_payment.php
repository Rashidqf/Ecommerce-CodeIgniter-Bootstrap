<?php defined('BASEPATH') OR exit('No direct script access allowed');
$sandbox = $paypal_sandbox == 1 ? '.sandbox.' : '.';
if (empty($cartItems['array'])) {
    redirect(base_url());
}
?>
<main class="buyora-container buyora-public">
    <section class="buyora-public-card buyora-auth-wrap">
        <h1><?= htmlspecialchars(lang('you_choose_paypal'), ENT_QUOTES, 'UTF-8') ?></h1>
        <form action="https://www<?= $sandbox ?>paypal.com/cgi-bin/webscr" method="post" target="_top" class="buyora-form">
            <input type="hidden" name="cmd" value="_cart">
            <input type="hidden" value="<?= htmlspecialchars($paypal_email, ENT_QUOTES, 'UTF-8') ?>" name="business">
            <input type="hidden" name="upload" value="1">
            <?php
            $discount = ($_SESSION['discountAmount'] == '' || $_SESSION['discountAmount'] == 0) ? false : $_SESSION['discountAmount'] / count($cartItems['array']);
            $i = 1;
            foreach ($cartItems['array'] as $item) { ?>
                <input type="hidden" name="item_name_<?= $i ?>" value="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="amount_<?= $i ?>" value="<?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="discount_amount_<?= $i ?>" value="<?= $discount !== false ? $discount : 0 ?>">
                <input type="hidden" name="quantity_<?= $i ?>" value="<?= (int) $item['num_added'] ?>">
            <?php $i++; } ?>
            <input type="hidden" name="currency_code" value="<?= CURRENCY_KEY ?>">
            <input type="hidden" value="utf-8" name="charset">
            <input type="hidden" value="<?= base_url('checkout/paypal_success') ?>" name="return">
            <input type="hidden" value="<?= base_url('checkout/paypal_cancel') ?>" name="cancel_return">
            <input type="hidden" value="authorization" name="paymentaction">
            <button class="buyora-button buyora-button-primary" type="submit"><?= htmlspecialchars(lang('go_to_paypal'), ENT_QUOTES, 'UTF-8') ?></button>
        </form>
        <nav class="buyora-inline-links"><a href="<?= base_url('checkout/paypal_cancel') ?>"><?= htmlspecialchars(lang('cancel_payment'), ENT_QUOTES, 'UTF-8') ?></a></nav>
    </section>
</main>
