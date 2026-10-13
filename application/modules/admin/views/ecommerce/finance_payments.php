<div>
    <h1><i class="fa fa-credit-card" aria-hidden="true"></i> Safepay payments</h1>
    <hr>
    <div class="alert alert-info">
        Mode: <strong><?= htmlspecialchars($environment, ENT_QUOTES, 'UTF-8') ?></strong>.
        Intent: <strong><?= htmlspecialchars($intent, ENT_QUOTES, 'UTF-8') ?></strong>.
        Keys: <strong><?= $configured ? 'configured in the server environment' : 'missing' ?></strong>.
        Secrets are not stored in Buyora. Customer payments settle to the Safepay merchant account. Vendor payouts are recorded separately and are not sent automatically.
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead><tr><th>Order</th><th>Provider</th><th>Status</th><th>Amount</th><th>Tracker</th><th>Created</th></tr></thead>
            <tbody>
            <?php if (empty($transactions)) { ?><tr><td colspan="6">No payment attempts yet.</td></tr><?php } ?>
            <?php foreach ($transactions as $transaction) { ?>
                <tr>
                    <td><?= (int) $transaction['order_number'] ?></td>
                    <td><?= htmlspecialchars($transaction['provider'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($transaction['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>Rs. <?= htmlspecialchars(Order_quote::formatMinor($transaction['amount_minor']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $transaction['tracker'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= date('d.m.Y H:i', (int) $transaction['created_at']) ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
