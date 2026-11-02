<script>
if (window.self !== window.top) {
    try {
        window.top.location.href = window.location.href;
    } catch (e) {}
}
</script>
<div class="buyora-public">
    <section class="buyora-panel">
        <?php if (!empty($paid)) { ?>
            <div style="text-align: center; padding: 20px 0;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: #ecfdf5; border-radius: 50%; color: #059669; font-size: 32px; margin-bottom: 16px;">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                </div>
                <h1 style="color: #059669; font-size: 24px; font-weight: 700; margin-bottom: 8px;">Payment Received Successfully!</h1>
                <p style="color: #475569; font-size: 15px; margin-bottom: 24px;">
                    Bank Alfalah has confirmed your payment of <strong>PKR <?= htmlspecialchars((string) $amount, ENT_QUOTES, 'UTF-8') ?></strong> for Order #<?= htmlspecialchars((string) $order_id, ENT_QUOTES, 'UTF-8') ?>.
                    <?php if (!empty($auth_code)) { ?>
                        <br><span style="font-size: 13px; color: #64748b;">Approval Code: <?= htmlspecialchars((string) $authCode, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php } ?>
                </p>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <a class="buyora-button buyora-button-primary" href="<?= base_url('order-tracking?order_id=' . rawurlencode($order_id)) ?>">Track Your Order</a>
                    <a class="buyora-button" style="background: #f1f5f9; color: #334155;" href="<?= base_url() ?>">Continue Shopping</a>
                </div>
            </div>
        <?php } else { ?>
            <div style="text-align: center; padding: 20px 0;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: #fef2f2; border-radius: 50%; color: #dc2626; font-size: 32px; margin-bottom: 16px;">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                </div>
                <h1 style="color: #dc2626; font-size: 24px; font-weight: 700; margin-bottom: 8px;">Payment Incomplete or Declined</h1>
                <p style="color: #475569; font-size: 15px; margin-bottom: 24px;">
                    <?= htmlspecialchars((string) (!empty($error) ? $error : 'The payment transaction could not be verified by Bank Alfalah.'), ENT_QUOTES, 'UTF-8') ?>
                </p>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <a class="buyora-button buyora-button-primary" href="<?= htmlspecialchars(LANG_URL . '/checkout', ENT_QUOTES, 'UTF-8') ?>">Retry Payment</a>
                    <a class="buyora-button" style="background: #f1f5f9; color: #334155;" href="<?= base_url() ?>">Back to Shop</a>
                </div>
            </div>
        <?php } ?>
    </section>
</div>

