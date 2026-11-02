<script>
if (window.self !== window.top) {
    try {
        window.top.location.href = window.location.href;
    } catch (e) {}
}
</script>
<div class="buyora-public">
    <section class="buyora-panel">
        <div style="text-align: center; padding: 20px 0;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: #fffbeb; border-radius: 50%; color: #d97706; font-size: 32px; margin-bottom: 16px;">
                <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
            </div>
            <h1 style="color: #1e293b; font-size: 24px; font-weight: 700; margin-bottom: 8px;">Payment Cancelled</h1>
            <p style="color: #475569; font-size: 15px; margin-bottom: 24px;">
                Your Bank Alfalah transaction was cancelled. Your cart items are preserved and you can complete the purchase whenever you are ready.
            </p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <a class="buyora-button buyora-button-primary" href="<?= htmlspecialchars(LANG_URL . '/checkout', ENT_QUOTES, 'UTF-8') ?>">Return to Checkout</a>
                <a class="buyora-button" style="background: #f1f5f9; color: #334155;" href="<?= base_url() ?>">Continue Shopping</a>
            </div>
        </div>
    </section>
</div>

