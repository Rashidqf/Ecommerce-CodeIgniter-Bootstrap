<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$checkoutProducts = isset($cartItems['array']) && is_array($cartItems['array']) ? $cartItems['array'] : array();
$safepayReady = !empty($safepay_ready);
$safepayEnvironment = !empty($safepay_environment) ? $safepay_environment : 'sandbox';
$bankReady = isset($bank_account['iban']) && $bank_account['iban'] != '';
$parsedTotal = !empty($cartItems['finalSum']) ? (float) str_replace(array(' ', ','), '', $cartItems['finalSum']) : 0;
$shippingShown = (int) $shippingAmount > 0 && (float) $shippingOrder > $parsedTotal;
?>
<main class="buyora-container buyora-page" id="main-content">
    <div class="buyora-page-heading">
        <div><h1><?= htmlspecialchars(lang('checkout'), ENT_QUOTES, 'UTF-8') ?></h1></div>
        <a class="buyora-text-link buyora-checkout-basket-link" href="<?= htmlspecialchars(LANG_URL . '/shopping-cart', ENT_QUOTES, 'UTF-8') ?>"><i class="fa fa-plus" aria-hidden="true"></i> <?= htmlspecialchars(lang('shopping_cart'), ENT_QUOTES, 'UTF-8') ?></a>
    </div>
    <?php $errors = $this->session->flashdata('submit_error'); ?>
    <?php if (is_array($errors) && !empty($errors)) { ?>
        <div class="buyora-form-errors" role="alert"><strong><?= htmlspecialchars(lang('finded_errors'), ENT_QUOTES, 'UTF-8') ?></strong>
            <?php foreach ($errors as $error) { ?><div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
        </div>
    <?php } ?>
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
        <form method="POST" id="goOrder" class="buyora-checkout-layout" novalidate>
            <section class="buyora-checkout-panel buyora-checkout-delivery">
                <h2>Delivery and payment</h2>
                <div class="buyora-checkout-fields">
                    <label class="buyora-field buyora-field-wide">
                        <span><?= htmlspecialchars(lang('choose_payment'), ENT_QUOTES, 'UTF-8') ?></span>
                        <select name="payment_type" id="buyora-payment-type">
                            <?php if ($safepayReady) { ?><option value="Safepay" selected>Card (Visa / Mastercard)</option><?php } ?>
                            <?php if ($bankReady) { ?><option value="Bank"><?= htmlspecialchars(lang('bank_payment'), ENT_QUOTES, 'UTF-8') ?></option><?php } ?>
                        </select>
                        <?php if (!$safepayReady) { ?><small>Safepay is not configured yet. Add the sandbox keys on the server.</small><?php } ?>
                    </label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('first_name'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="firstNameInput" name="first_name" type="text" autocomplete="given-name" value="<?= htmlspecialchars((string) (isset($_POST['first_name']) ? $_POST['first_name'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('last_name'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="lastNameInput" name="last_name" type="text" autocomplete="family-name" value="<?= htmlspecialchars((string) (isset($_POST['last_name']) ? $_POST['last_name'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('email_address'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="emailAddressInput" name="email" type="email" autocomplete="email" value="<?= htmlspecialchars((string) (isset($_POST['email']) ? $_POST['email'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('phone'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="phoneInput" name="phone" type="tel" autocomplete="tel" value="<?= htmlspecialchars((string) (isset($_POST['phone']) ? $_POST['phone'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field buyora-field-wide"><span><?= htmlspecialchars(lang('address'), ENT_QUOTES, 'UTF-8') ?> *</span><textarea id="addressInput" name="address" rows="3" autocomplete="street-address" required><?= htmlspecialchars((string) (isset($_POST['address']) ? $_POST['address'] : ''), ENT_QUOTES, 'UTF-8') ?></textarea></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('city'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="cityInput" name="city" type="text" autocomplete="address-level2" value="<?= htmlspecialchars((string) (isset($_POST['city']) ? $_POST['city'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('post_code'), ENT_QUOTES, 'UTF-8') ?></span><input id="postInput" name="post_code" type="text" autocomplete="postal-code" value="<?= htmlspecialchars((string) (isset($_POST['post_code']) ? $_POST['post_code'] : ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                    <label class="buyora-field buyora-field-wide"><span><?= htmlspecialchars(lang('notes'), ENT_QUOTES, 'UTF-8') ?></span><textarea id="notesInput" name="notes" rows="3"><?= htmlspecialchars((string) (isset($_POST['notes']) ? $_POST['notes'] : ''), ENT_QUOTES, 'UTF-8') ?></textarea></label>
                </div>
                <?php if ((int) $codeDiscounts === 1) { ?>
                    <div class="buyora-discount-form">
                        <label for="buyora-discount-code"><?= htmlspecialchars(lang('discount_code'), ENT_QUOTES, 'UTF-8') ?></label>
                        <div><input id="buyora-discount-code" name="discountCode" value="<?= htmlspecialchars((string) (isset($_POST['discountCode']) ? $_POST['discountCode'] : ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars(lang('enter_discount_code'), ENT_QUOTES, 'UTF-8') ?>"><button type="button" onclick="checkDiscountCode()"><?= htmlspecialchars(lang('check_code'), ENT_QUOTES, 'UTF-8') ?></button></div>
                    </div>
                <?php } ?>
            </section>

            <div class="buyora-checkout-aside">
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
                    <div class="buyora-order-total"><span><?= htmlspecialchars(lang('total'), ENT_QUOTES, 'UTF-8') ?></span><strong><span class="final-amount" id="buyora-order-total"><?= htmlspecialchars($cartItems['finalSum'], ENT_QUOTES, 'UTF-8') ?></span><?= CURRENCY ?></strong></div>
                    <?php if ($shippingShown) { ?>
                        <div class="buyora-order-total"><span><?= htmlspecialchars(lang('shipping'), ENT_QUOTES, 'UTF-8') ?></span><strong><span class="final-amount"><?= (int) $shippingAmount ?></span><?= CURRENCY ?></strong></div>
                    <?php } ?>
                    <input type="hidden" class="final-amount" name="final_amount" value="<?= htmlspecialchars($cartItems['finalSum'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="amount_currency" value="<?= htmlspecialchars(CURRENCY, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="discountAmount" value="">
                    <button class="buyora-button buyora-button-primary buyora-place-order" type="submit" id="buyora-bank-submit" <?= $safepayReady ? 'hidden' : '' ?>><?= htmlspecialchars(lang('custom_order'), ENT_QUOTES, 'UTF-8') ?> <i class="fa fa-lock" aria-hidden="true"></i></button>
                </aside>

                <?php if ($safepayReady) { ?>
                    <section class="buyora-checkout-panel buyora-card-pay" id="buyora-card-pay" aria-labelledby="buyora-card-pay-title">
                        <div class="buyora-card-pay-head">
                            <h3 id="buyora-card-pay-title">Add a payment method</h3>
                            <button type="button" class="buyora-text-link buyora-card-cancel" id="buyora-card-refresh">Cancel</button>
                        </div>
                        <div class="buyora-card-frame" aria-live="polite">
                            <safepay-card-atom id="buyora-safepay-card"></safepay-card-atom>
                        </div>
                        <p class="buyora-card-hint" id="buyora-card-hint">Enter your card details</p>
                        <div class="buyora-card-status" id="buyora-card-status" role="status" aria-live="polite" hidden></div>
                        <div class="buyora-card-actions">
                            <button type="button" class="buyora-button buyora-button-primary" id="buyora-pay-securely" disabled>
                                <span class="buyora-pay-label">Pay Securely</span>
                                <i class="fa fa-lock" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="buyora-text-link buyora-hosted-fallback" id="buyora-hosted-fallback">Use Safepay hosted checkout instead</button>
                        </div>
                    </section>
                <?php } ?>
            </div>
        </form>

        <div id="buyora-threeds-modal" class="buyora-threeds-modal" hidden>
            <div class="buyora-threeds-dialog" role="dialog" aria-modal="true" aria-labelledby="buyora-threeds-title">
                <h2 id="buyora-threeds-title">Bank authentication</h2>
                <p>Complete the verification with your bank to finish this payment.</p>
                <div class="buyora-threeds-frame">
                    <safepay-payer-auth-atom id="buyora-safepay-auth"></safepay-payer-auth-atom>
                </div>
                <button type="button" class="buyora-text-link" id="buyora-threeds-close">Cancel authentication</button>
            </div>
        </div>

        <?php if ((int) $codeDiscounts === 1 && isset($_POST['discountCode'])) { ?>
            <script>$(function () { checkDiscountCode(); });</script>
        <?php } ?>
        <?php if ($safepayReady) { ?>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sfpy/atoms@0.3.7/dist/styles/index.css">
            <script src="https://cdn.jsdelivr.net/npm/@sfpy/atoms@0.3.7/dist/components/index.global.js"></script>
            <script>
                window.buyoraSafepay = {
                    prepareUrl: <?= json_encode(isset($safepay_prepare_url) ? $safepay_prepare_url : base_url('checkout/safepay/prepare')) ?>,
                    startUrl: <?= json_encode(isset($safepay_start_url) ? $safepay_start_url : base_url('checkout/safepay/start')) ?>,
                    environment: <?= json_encode($safepayEnvironment) ?>,
                    csrfName: <?= json_encode($this->security->get_csrf_token_name()) ?>,
                    csrfHash: <?= json_encode($this->security->get_csrf_hash()) ?>
                };
            </script>
            <script src="<?= base_url('assets/buyora/buyora-safepay.js') ?>"></script>
        <?php } ?>
    <?php } ?>
</main>
