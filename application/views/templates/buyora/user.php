<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<main class="buyora-container buyora-public">
    <div class="buyora-split">
        <section class="buyora-panel">
            <h1><?= htmlspecialchars(lang('my_acc'), ENT_QUOTES, 'UTF-8') ?></h1>
            <form class="buyora-form" method="POST" action="">
                <label for="account-name"><?= htmlspecialchars(lang('name'), ENT_QUOTES, 'UTF-8') ?></label>
                <input id="account-name" type="text" name="name" value="<?= htmlspecialchars($userInfo['name'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="account-phone"><?= htmlspecialchars(lang('phone'), ENT_QUOTES, 'UTF-8') ?></label>
                <input id="account-phone" type="text" name="phone" value="<?= htmlspecialchars($userInfo['phone'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="account-email"><?= htmlspecialchars(lang('email_address'), ENT_QUOTES, 'UTF-8') ?></label>
                <input id="account-email" type="email" name="email" value="<?= htmlspecialchars($userInfo['email'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="account-pass"><?= htmlspecialchars(lang('password'), ENT_QUOTES, 'UTF-8') ?></label>
                <input id="account-pass" type="password" name="pass" placeholder="Leave blank to keep the current password">
                <button class="buyora-button buyora-button-primary" type="submit" name="update"><?= htmlspecialchars(lang('update'), ENT_QUOTES, 'UTF-8') ?></button>
            </form>
            <nav class="buyora-inline-links"><a href="<?= LANG_URL . '/logout' ?>"><?= htmlspecialchars(lang('logout'), ENT_QUOTES, 'UTF-8') ?></a></nav>
        </section>
        <section class="buyora-panel">
            <h2><?= htmlspecialchars(lang('user_order_history'), ENT_QUOTES, 'UTF-8') ?></h2>
            <table class="buyora-table">
                <thead>
                    <tr>
                        <th><?= htmlspecialchars(lang('usr_order_id'), ENT_QUOTES, 'UTF-8') ?></th>
                        <th><?= htmlspecialchars(lang('usr_order_date'), ENT_QUOTES, 'UTF-8') ?></th>
                        <th><?= htmlspecialchars(lang('usr_order_address'), ENT_QUOTES, 'UTF-8') ?></th>
                        <th><?= htmlspecialchars(lang('user_order_products'), ENT_QUOTES, 'UTF-8') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders_history)) {
                        foreach ($orders_history as $order) { ?>
                            <tr>
                                <td><?= (int) $order['order_id'] ?></td>
                                <td><?= date('d.m.Y', $order['date']) ?></td>
                                <td><?= htmlspecialchars($order['address'], ENT_QUOTES, 'UTF-8') ?><br><?= htmlspecialchars($order['phone'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php $arr_products = unserialize($order['products']);
                                    if (is_array($arr_products)) {
                                        foreach ($arr_products as $product) {
                                            if (empty($product['product_info'])) {
                                                continue;
                                            } ?>
                                            <div class="buyora-order-item">
                                                <img src="<?= base_url('attachments/shop_images/' . rawurlencode($product['product_info']['image'])) ?>" alt="">
                                                <span><?= htmlspecialchars(lang('user_order_quantity'), ENT_QUOTES, 'UTF-8') ?> <?= (int) $product['product_quantity'] ?></span>
                                            </div>
                                        <?php }
                                    } ?>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr><td colspan="4"><?= htmlspecialchars(lang('usr_no_orders'), ENT_QUOTES, 'UTF-8') ?></td></tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php if (!empty($links_pagination)) { ?><div class="buyora-pagination"><?= $links_pagination ?></div><?php } ?>
        </section>
    </div>
</main>
