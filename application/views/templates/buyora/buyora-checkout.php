<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$checkoutProducts = isset($cartItems['array']) && is_array($cartItems['array']) ? $cartItems['array'] : array();
$alfalahReady = !empty($alfalah_ready);
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
        <div class="buyora-form-errors" id="server-errors" role="alert"><strong><?= htmlspecialchars(lang('finded_errors'), ENT_QUOTES, 'UTF-8') ?></strong>
            <?php foreach ($errors as $error) { ?><div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
        </div>
    <?php } else { ?>
        <div class="buyora-form-errors" id="server-errors" role="alert" style="display: none;"></div>
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
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('first_name'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="firstNameInput" name="first_name" type="text" autocomplete="given-name" value="<?= htmlspecialchars((string) (isset($_POST['first_name']) ? $_POST['first_name'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('last_name'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="lastNameInput" name="last_name" type="text" autocomplete="family-name" value="<?= htmlspecialchars((string) (isset($_POST['last_name']) ? $_POST['last_name'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('email_address'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="emailAddressInput" name="email" type="email" autocomplete="email" value="<?= htmlspecialchars((string) (isset($_POST['email']) ? $_POST['email'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('phone'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="phoneInput" name="phone" type="tel" autocomplete="tel" value="<?= htmlspecialchars((string) (isset($_POST['phone']) ? $_POST['phone'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field buyora-field-wide"><span><?= htmlspecialchars(lang('address'), ENT_QUOTES, 'UTF-8') ?> *</span><textarea id="addressInput" name="address" rows="2" autocomplete="street-address" required><?= htmlspecialchars((string) (isset($_POST['address']) ? $_POST['address'] : ''), ENT_QUOTES, 'UTF-8') ?></textarea></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('city'), ENT_QUOTES, 'UTF-8') ?> *</span><input id="cityInput" name="city" type="text" autocomplete="address-level2" value="<?= htmlspecialchars((string) (isset($_POST['city']) ? $_POST['city'] : ''), ENT_QUOTES, 'UTF-8') ?>" required></label>
                    <label class="buyora-field"><span><?= htmlspecialchars(lang('post_code'), ENT_QUOTES, 'UTF-8') ?></span><input id="postInput" name="post_code" type="text" autocomplete="postal-code" value="<?= htmlspecialchars((string) (isset($_POST['post_code']) ? $_POST['post_code'] : ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                    <label class="buyora-field buyora-field-wide"><span><?= htmlspecialchars(lang('notes'), ENT_QUOTES, 'UTF-8') ?></span><textarea id="notesInput" name="notes" rows="2"><?= htmlspecialchars((string) (isset($_POST['notes']) ? $_POST['notes'] : ''), ENT_QUOTES, 'UTF-8') ?></textarea></label>
                </div>

                <!-- Hidden payment type input controlled by tabs -->
                <input type="hidden" name="payment_type" id="buyora-payment-type" value="BankAlfalah">

                <!-- In-Page Card Payment UI Component -->
                <div class="buyora-card-checkout-container" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <span style="font-size: 14px; font-weight: 700; color: #1e293b;">Payment method</span>
                        <div class="buyora-card-badges" style="display: flex; gap: 8px;">
                            <div class="card-badge active" id="badge-mastercard" style="border: 1.5px solid #8b5cf6; border-radius: 8px; padding: 4px 10px; display: inline-flex; align-items: center; justify-content: center; background: #faf5ff; height: 32px; min-width: 52px; cursor: pointer;">
                                <svg width="28" height="18" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="11" fill="#EB001B"/>
                                    <circle cx="24" cy="12" r="11" fill="#F79E1B" fill-opacity="0.9"/>
                                </svg>
                            </div>
                            <div class="card-badge" id="badge-visa" style="border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 4px 10px; display: inline-flex; align-items: center; justify-content: center; background: #ffffff; height: 32px; min-width: 52px; cursor: pointer;">
                                <span style="font-weight: 900; font-style: italic; color: #1a1f71; font-size: 15px; letter-spacing: 0.5px;">VISA</span>
                            </div>
                            <div class="card-badge" id="badge-paypak" style="border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 4px 10px; display: inline-flex; align-items: center; justify-content: center; background: #ffffff; height: 32px; min-width: 52px; cursor: pointer;">
                                <span style="font-weight: 800; color: #059669; font-size: 11px; letter-spacing: 0.5px;">PayPak</span>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Card Inputs matching user mockup -->
                    <div class="buyora-card-form" style="display: flex; flex-direction: column; gap: 14px;">
                        <div class="buyora-card-field">
                            <label for="card_number" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Card number</label>
                            <div style="position: relative;">
                                <input type="tel" id="card_number" name="card_number" placeholder="1111 2222 3333 4444" maxlength="19" autocomplete="cc-number"
                                    style="width: 100%; height: 44px; padding: 0 42px 0 14px; border: 1.5px solid #8b5cf6; border-radius: 8px; font-size: 15px; font-family: monospace; letter-spacing: 1.5px; outline: none; background: #ffffff; box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.08);">
                                <span id="card-type-icon" style="position: absolute; right: 12px; top: 12px; font-size: 18px; color: #8b5cf6;">
                                    <i class="fa fa-credit-card"></i>
                                </span>
                            </div>
                        </div>

                        <div class="buyora-card-field">
                            <label for="card_holder" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Cardholder’s name</label>
                            <input type="text" id="card_holder" name="card_holder" placeholder="JOHN DOE" autocomplete="cc-name"
                                style="width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid #8b5cf6; border-radius: 8px; font-size: 14px; text-transform: uppercase; outline: none; background: #ffffff;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                            <div class="buyora-card-field">
                                <label for="card_expiry" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">Expiry date</label>
                                <input type="tel" id="card_expiry" name="card_expiry" placeholder="MM / YY" maxlength="7" autocomplete="cc-exp"
                                    style="width: 100%; height: 44px; padding: 0 14px; border: 1.5px solid #8b5cf6; border-radius: 8px; font-size: 14px; font-family: monospace; outline: none; background: #ffffff;">
                            </div>
                            <div class="buyora-card-field">
                                <label for="card_cvv" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">CVV</label>
                                <div style="position: relative;">
                                    <input type="password" id="card_cvv" name="card_cvv" placeholder="123" maxlength="4" autocomplete="cc-csc"
                                        style="width: 100%; height: 44px; padding: 0 36px 0 14px; border: 1.5px solid #8b5cf6; border-radius: 8px; font-size: 14px; font-family: monospace; outline: none; background: #ffffff;">
                                    <span style="position: absolute; right: 12px; top: 13px; font-size: 14px; color: #94a3b8;">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 10px; margin-top: 4px;">
                            <input type="checkbox" id="save_card" name="save_card" checked style="width: 18px; height: 18px; accent-color: #8b5cf6; cursor: pointer;">
                            <label for="save_card" style="font-size: 13px; color: #64748b; cursor: pointer; user-select: none; margin: 0;">Save information for future payments</label>
                        </div>
                    </div>
                </div>

                <?php if ((int) $codeDiscounts === 1) { ?>
                    <div class="buyora-discount-form" style="margin-top: 20px;">
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
                    
                    <!-- Checkout Button matching user mockup -->
                    <button class="buyora-button buyora-button-primary buyora-place-order" type="submit" id="buyora-checkout-btn"
                        style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border: none; height: 50px; font-size: 16px; font-weight: 700; border-radius: 10px; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35); transition: all 0.2s ease;">
                        <span id="btn-text">Checkout <i class="fa fa-lock" style="margin-left: 6px;"></i></span>
                    </button>
                    
                    <div style="text-align: center; font-size: 12px; color: #64748b; margin-top: 14px; line-height: 1.5;">
                        <i class="fa fa-shield" style="color: #059669;"></i> <strong>Bank Alfalah Verified</strong><br>
                        256-Bit SSL Encrypted & Protected
                    </div>
                </aside>
            </div>
        </form>

        <!-- In-Page Seamless 3DS Authentication Modal -->
        <div id="buyora3dsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
            <div style="background: #ffffff; width: 100%; max-width: 540px; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; animation: modalSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                <div style="background: #f8fafc; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="<?= base_url('assets/buyora/buyora-logo.png') ?>" alt="BUYORA" style="height: 24px;">
                        <span style="font-size: 13px; font-weight: 700; color: #1e293b;">Bank Alfalah 3D-Secure</span>
                    </div>
                    <button type="button" id="close3dsModal" style="background: transparent; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; line-height: 1;">&times;</button>
                </div>
                <div style="padding: 0; min-height: 480px; position: relative;">
                    <div id="frameLoader" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #ffffff; z-index: 1;">
                        <div style="width: 44px; height: 44px; border: 4px solid #e2e8f0; border-top-color: #8b5cf6; border-radius: 50%; animation: modalSpin 0.8s linear infinite; margin-bottom: 16px;"></div>
                        <span style="font-size: 14px; font-weight: 600; color: #334155;">Securing connection with Bank Alfalah...</span>
                        <span style="font-size: 12px; color: #64748b; margin-top: 4px;">Please do not refresh or close this window</span>
                    </div>
                    <iframe id="buyora3dsFrame" name="buyora3dsFrame" style="width: 100%; height: 480px; border: none; display: block;" onload="document.getElementById('frameLoader').style.display='none';"></iframe>
                </div>
            </div>
        </div>

        <style>
            @keyframes modalSlideIn {
                from { transform: translateY(20px) scale(0.97); opacity: 0; }
                to { transform: translateY(0) scale(1); opacity: 1; }
            }
            @keyframes modalSpin {
                to { transform: rotate(360deg); }
            }
            .card-badge.active {
                border-color: #8b5cf6 !important;
                background: #faf5ff !important;
                box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);
            }
        </style>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('goOrder');
            var cardNum = document.getElementById('card_number');
            var cardExp = document.getElementById('card_expiry');
            var cardCvv = document.getElementById('card_cvv');
            var cardHolder = document.getElementById('card_holder');
            var modal = document.getElementById('buyora3dsModal');
            var closeBtn = document.getElementById('close3dsModal');
            var btnCheckout = document.getElementById('buyora-checkout-btn');
            var btnText = document.getElementById('btn-text');

            // Format card number with spaces every 4 digits & live badge detection
            cardNum.addEventListener('input', function (e) {
                var val = e.target.value.replace(/\D/g, '').substring(0, 16);
                var parts = [];
                for (var i = 0; i < val.length; i += 4) {
                    parts.push(val.substring(i, i + 4));
                }
                e.target.value = parts.join(' ');

                // Brand detection
                document.querySelectorAll('.card-badge').forEach(function(b) { b.classList.remove('active'); });
                if (val.startsWith('4')) {
                    document.getElementById('badge-visa').classList.add('active');
                } else if (val.startsWith('5') || val.startsWith('2')) {
                    document.getElementById('badge-mastercard').classList.add('active');
                } else if (val.startsWith('9')) {
                    document.getElementById('badge-paypak').classList.add('active');
                } else {
                    document.getElementById('badge-mastercard').classList.add('active');
                }
            });

            // Format expiry date as MM / YY
            cardExp.addEventListener('input', function (e) {
                var val = e.target.value.replace(/\D/g, '').substring(0, 4);
                if (val.length >= 2) {
                    e.target.value = val.substring(0, 2) + ' / ' + val.substring(2);
                } else {
                    e.target.value = val;
                }
            });

            // Restrict CVV to numbers
            cardCvv.addEventListener('input', function (e) {
                e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
            });

            // Close modal handler
            closeBtn.addEventListener('click', function () {
                modal.style.display = 'none';
                btnCheckout.disabled = false;
                btnText.innerHTML = 'Checkout <i class="fa fa-lock" style="margin-left: 6px;"></i>';
            });

            // In-Page Checkout Submission with 3DS Modal
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                // Simple client validation
                var errorsDiv = document.getElementById('server-errors');
                errorsDiv.style.display = 'none';
                errorsDiv.innerHTML = '';

                var fn = document.getElementById('firstNameInput').value.trim();
                var ln = document.getElementById('lastNameInput').value.trim();
                var em = document.getElementById('emailAddressInput').value.trim();
                var ph = document.getElementById('phoneInput').value.trim();
                var ad = document.getElementById('addressInput').value.trim();
                var ci = document.getElementById('cityInput').value.trim();
                var cNum = cardNum.value.replace(/\s/g, '');
                var cExp = cardExp.value.replace(/\s/g, '');
                var cCvv = cardCvv.value.trim();

                var clientErrors = [];
                if (!fn) clientErrors.push('First name is required.');
                if (!ln) clientErrors.push('Last name is required.');
                if (!em || em.indexOf('@') === -1) clientErrors.push('Valid email address is required.');
                if (!ph) clientErrors.push('Phone number is required.');
                if (!ad) clientErrors.push('Delivery address is required.');
                if (!ci) clientErrors.push('City is required.');
                if (cNum.length < 13) clientErrors.push('Please enter a valid card number.');
                if (cExp.length < 5) clientErrors.push('Please enter expiry date as MM / YY.');
                if (cCvv.length < 3) clientErrors.push('Please enter 3 or 4 digit CVV.');

                if (clientErrors.length > 0) {
                    errorsDiv.innerHTML = '<strong>Please correct the following:</strong>' + clientErrors.map(function(err){ return '<div>' + err + '</div>'; }).join('');
                    errorsDiv.style.display = 'block';
                    errorsDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                btnCheckout.disabled = true;
                btnText.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

                // AJAX submission
                var formData = new FormData(form);
                formData.append('is_ajax', '1');

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (!data.ok) {
                        btnCheckout.disabled = false;
                        btnText.innerHTML = 'Checkout <i class="fa fa-lock" style="margin-left: 6px;"></i>';
                        errorsDiv.innerHTML = '<strong>Error:</strong>' + (data.errors ? data.errors.map(function(err){ return '<div>' + err + '</div>'; }).join('') : '<div>Could not process checkout.</div>');
                        errorsDiv.style.display = 'block';
                        errorsDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return;
                    }

                    // Bank transfer direct redirect
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                        return;
                    }

                    // Open Bank Alfalah in-page 3DS modal
                    modal.style.display = 'flex';
                    document.getElementById('frameLoader').style.display = 'flex';

                    // Dynamically build and post hidden form targeting the iframe
                    var hiddenForm = document.createElement('form');
                    hiddenForm.method = 'POST';
                    hiddenForm.action = data.gateway_url;
                    hiddenForm.target = 'buyora3dsFrame';
                    hiddenForm.style.display = 'none';

                    for (var key in data.payload) {
                        if (data.payload.hasOwnProperty(key)) {
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = data.payload[key];
                            hiddenForm.appendChild(input);
                        }
                    }

                    document.body.appendChild(hiddenForm);
                    hiddenForm.submit();
                    document.body.removeChild(hiddenForm);
                })
                .catch(function (err) {
                    btnCheckout.disabled = false;
                    btnText.innerHTML = 'Checkout <i class="fa fa-lock" style="margin-left: 6px;"></i>';
                    errorsDiv.innerHTML = '<strong>Connection Error:</strong><div>Unable to reach checkout server. Please try again.</div>';
                    errorsDiv.style.display = 'block';
                });
            });
        });
        </script>
    <?php } ?>
</main>
