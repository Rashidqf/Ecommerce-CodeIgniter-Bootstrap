<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$checkoutProducts = isset($cartItems['array']) && is_array($cartItems['array']) ? $cartItems['array'] : array();
?>
<main class="buyora-container buyora-page">
    <div class="buyora-page-heading">
        <div><div class="buyora-eyebrow">Almost there</div><h1><?= htmlspecialchars(lang('checkout'), ENT_QUOTES, 'UTF-8') ?></h1></div>
        <a class="buyora-text-link" href="<?= htmlspecialchars(LANG_URL . '/shopping-cart', ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?= htmlspecialchars(lang('shopping_cart'), ENT_QUOTES, 'UTF-8') ?></a>
    </div>
    <?php if (empty($checkoutProducts)) { ?>
        <section class="buyora-empty-state buyora-cart-empty">
            <i class="fa fa-shopping-bag" aria-hidden="true"></i>
            <h2><?= htmlspecialchars(lang('no_products_in_cart'), ENT_QUOTES, 'UTF-8') ?></h2>
            <a class="buyora-button buyora-button-primary" href="<?= base_url() ?>">Continue shopping</a>
        </section>
    <?php } else { ?>
        <?php if (!empty($shippingOrder)) { ?>
            <div class="buyora-shipping-note">
                <strong><?= htmlspecialchars(lang('freeShippingHeader'), ENT_QUOTES, 'UTF-8') ?></strong>
                <span><?= htmlspecialchars(str_replace(array('%price%', '%currency%'), array($shippingOrder, CURRENCY), lang('freeShipping')), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php } ?>
        <?php $errors = $this->session->flashdata('submit_error'); ?>
        <?php if (is_array($errors) && !empty($errors)) { ?>
            <div class="buyora-form-errors"><strong><?= htmlspecialchars(lang('finded_errors'), ENT_QUOTES, 'UTF-8') ?></strong>
                <?php foreach ($errors as $error) { ?><div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
            </div>
        <?php } ?>
        <form method="POST" id="goOrder" class="buyora-checkout-layout">
            <section class="buyora-checkout-panel">
                <h2>Delivery and payment</h2>
                <div class="buyora-checkout-fields">
                    <label class="buyora-field buyora-field-wide">
                        <span><?= htmlspecialchars(lang('choose_payment'), ENT_QUOTES, 'UTF-8') ?></span>
                        <select name="payment_type">
                            <?php if ($cashondelivery_visibility == 1) { ?><option value="cashOnDelivery"><?= htmlspecialchars(lang('cash_on_delivery'), ENT_QUOTES, 'UTF-8') ?></option><?php } ?>
                            <?php if (filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) { ?><option value="PayPal"><?= htmlspecialchars(lang('paypal'), ENT_QUOTES, 'UTF-8') ?></option><?php } ?>
                            <?php if (isset($bank_account['iban']) && $bank_account['iban'] != '') { ?><option value="Bank"><?= htmlspecialchars(lang('bank_payment'), ENT_QUOTES, 'UTF-8') ?></option><?php } ?>
                        </select>
                    </label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('first_name'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="firstNameInput" name="first_name" type="text" value="<?= htmlspecialchars((string) (isset($_POST['first_name']) ? $_POST['first_name'] : ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('last_name'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="lastNameInput" name="last_name" type="text" value="<?= htmlspecialchars((string) (isset($_POST['last_name']) ? $_POST['last_name'] : ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('email_address'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="emailAddressInput" name="email" type="text" value="<?= htmlspecialchars((string) (isset($_POST['email']) ? $_POST['email'] : ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('phone'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="phoneInput" name="phone" type="text" value="<?= htmlspecialchars((string) (isset($_POST['phone']) ? $_POST['phone'] : ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                    <label class="buyora-field buyora-field-wide"><span><?= htmlspecialchars(lang('address'), ENT_QUOTES, 'UTF-8') ?> *</span><textarea id="addressInput" name="address" rows="3"><?= htmlspecialchars((string) (isset($_POST['address']) ? $_POST['address'] : ''), ENT_QUOTES, 'UTF-8') ?></textarea></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('city'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="cityInput" name="city" type="text" value="<?= htmlspecialchars((string) (isset($_POST['city']) ? $_POST['city'] : ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('post_code'), ENT_QUOTES, 'UTF-8') ?></span><input id="postInput" name="post_code" type="text" value="<?= htmlspecialchars((string) (isset($_POST['post_code']) ? $_POST['post_code'] : ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                    <label class="buyora-field buyora-field-wide"><span><?= htmlspecialchars(lang('notes'), ENT_QUOTES, 'UTF-8') ?></span><textarea id="notesInput" name="notes" rows="3"><?= htmlspecialchars((string) (isset($_POST['notes']) ? $_POST['notes'] : ''), ENT_QUOTES, 'UTF-8') ?></textarea></label>
                </div>
                <?php if ((int) $codeDiscounts === 1) { ?>
                    <div class="buyora-discount-form">
                        <label for="buyora-discount-code"><?= htmlspecialchars(lang('discount_code'), ENT_QUOTES, 'UTF-8') ?></label>
                        <div><input id="buyora-discount-code" name="discountCode" value="<?= htmlspecialchars((string) (isset($_POST['discountCode']) ? $_POST['discountCode'] : ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars(lang('enter_discount_code'), ENT_QUOTES, 'UTF-8') ?>"><button type="button" onclick="checkDiscountCode()"><?= htmlspecialchars(lang('check_code'), ENT_QUOTES, 'UTF-8') ?></button></div>
                    </div>
                <?php } ?>
            </section>
            <aside class="buyora-checkout-panel buyora-order-summary">
                <h2>Your order</h2>
                <?php foreach ($checkoutProducts as $item) {
                    $itemImage = !empty($item['image']) && is_file('attachments/shop_images/' . $item['image'])
                        ? base_url('attachments/shop_images/' . rawurlencode($item['image']))
                        : base_url('attachments/no-image-frontend.png');
                    ?>
                    <div class="buyora-order-item">
                        <img src="<?= htmlspecialchars($itemImage, ENT_QUOTES, 'UTF-8') ?>" alt="">
                        <div><strong><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></strong><span><?= (int) $item['num_added'] ?> × <?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8') . CURRENCY ?></span></div>
                        <b><?= htmlspecialchars($item['sum_price'], ENT_QUOTES, 'UTF-8') . CURRENCY ?></b>
                        <input type="hidden" name="id[]" value="<?= (int) $item['id'] ?>">
                        <input type="hidden" name="quantity[]" value="<?= (int) $item['num_added'] ?>">
                    </div>
                <?php } ?>
                <div class="buyora-order-total"><span><?= htmlspecialchars(lang('total'), ENT_QUOTES, 'UTF-8') ?></span><strong><span class="final-amount"><?= htmlspecialchars($cartItems['finalSum'], ENT_QUOTES, 'UTF-8') ?></span><?= CURRENCY ?></strong></div>
                <?php
                $parsedTotal = (float) str_replace(array(' ', ','), '', $cartItems['finalSum']);
                if ((int) $shippingAmount > 0 && (float) $shippingOrder > $parsedTotal) { ?>
                    <div class="buyora-order-total"><span><?= htmlspecialchars(lang('shipping'), ENT_QUOTES, 'UTF-8') ?></span><strong><span class="final-amount"><?= (int) $shippingAmount ?></span><?= CURRENCY ?></strong></div>
                <?php } ?>
                <input type="hidden" class="final-amount" name="final_amount" value="<?= htmlspecialchars($cartItems['finalSum'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="amount_currency" value="<?= htmlspecialchars(CURRENCY, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="discountAmount" value="">
                <button class="buyora-button buyora-button-primary buyora-place-order" type="button" onclick="document.getElementById('goOrder').submit();"><?= htmlspecialchars(lang('custom_order'), ENT_QUOTES, 'UTF-8') ?> <i class="fa fa-lock" aria-hidden="true"></i></button>
            </aside>
        </form>
        <?php if ((int) $codeDiscounts === 1 && isset($_POST['discountCode'])) { ?>
            <script>$(function () { checkDiscountCode(); });</script>
        <?php } ?>
    <?php } ?>
</main>
