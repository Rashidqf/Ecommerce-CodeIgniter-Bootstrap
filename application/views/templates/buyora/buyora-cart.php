<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$cartProducts = isset($cartItems['array']) && is_array($cartItems['array']) ? $cartItems['array'] : array();
?>
<main class="buyora-container buyora-page">
    <div class="buyora-page-heading">
        <div><div class="buyora-eyebrow">Your order</div><h1><?= htmlspecialchars(lang('shopping_cart'), ENT_QUOTES, 'UTF-8') ?></h1></div>
        <a class="buyora-text-link" href="<?= base_url() ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?= htmlspecialchars(lang('back_to_shop'), ENT_QUOTES, 'UTF-8') ?></a>
    </div>
    <?php if (empty($cartProducts)) { ?>
        <section class="buyora-empty-state buyora-cart-empty">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            <h2><?= htmlspecialchars(lang('no_products_in_cart'), ENT_QUOTES, 'UTF-8') ?></h2>
            <a class="buyora-button buyora-button-primary" href="<?= base_url() ?>">Continue shopping</a>
        </section>
    <?php } else { ?>
        <section class="buyora-cart-panel">
            <div class="buyora-cart-list">
                <?php foreach ($cartProducts as $item) {
                    $itemImage = !empty($item['image']) && is_file('attachments/shop_images/' . $item['image'])
                        ? base_url('attachments/shop_images/' . rawurlencode($item['image']))
                        : base_url('attachments/no-image-frontend.png');
                    $itemUrl = LANG_URL . '/' . $item['url'];
                    ?>
                    <article class="buyora-cart-row">
                        <a class="buyora-cart-product-image" href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"><img src="<?= htmlspecialchars($itemImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"></a>
                        <div class="buyora-cart-product">
                            <a href="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></a>
                            <span><?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8') . CURRENCY ?> each</span>
                            <strong>Item total: <?= htmlspecialchars($item['sum_price'], ENT_QUOTES, 'UTF-8') . CURRENCY ?></strong>
                        </div>
                        <div class="buyora-cart-controls" aria-label="Change quantity">
                            <a class="refresh-me add-to-cart buyora-quantity-button <?= (int) $item['quantity'] <= (int) $item['num_added'] ? 'disabled' : '' ?>" data-id="<?= (int) $item['id'] ?>" href="javascript:void(0);" aria-label="Increase quantity"><i class="fa fa-plus" aria-hidden="true"></i></a>
                            <span><?= (int) $item['num_added'] ?></span>
                            <a class="buyora-quantity-button" onclick="removeProduct(<?= (int) $item['id'] ?>, true)" href="javascript:void(0);" aria-label="Decrease quantity"><i class="fa fa-minus" aria-hidden="true"></i></a>
                            <a class="buyora-remove-button" href="<?= htmlspecialchars(base_url('home/removeFromCart?delete-product=' . (int) $item['id'] . '&back-to=shopping-cart'), ENT_QUOTES, 'UTF-8') ?>" aria-label="Remove product"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        </div>
                    </article>
                <?php } ?>
            </div>
            <div class="buyora-cart-summary">
                <div><span><?= htmlspecialchars(lang('total'), ENT_QUOTES, 'UTF-8') ?></span><strong><?= htmlspecialchars($cartItems['finalSum'], ENT_QUOTES, 'UTF-8') . CURRENCY ?></strong></div>
                <a class="buyora-button buyora-button-primary" href="<?= htmlspecialchars(LANG_URL . '/checkout', ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(lang('checkout'), ENT_QUOTES, 'UTF-8') ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </section>
    <?php } ?>
</main>
<?php if ($this->session->flashdata('deleted')) { ?>
    <script>$(function () { ShowNotificator('alert-info', <?= json_encode($this->session->flashdata('deleted')) ?>); });</script>
<?php } ?>
