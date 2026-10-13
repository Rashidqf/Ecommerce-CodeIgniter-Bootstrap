<div class="content">
    <div class="row">
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Eligible sales</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor($summary['sale']), ENT_QUOTES, 'UTF-8') ?></div></div></div>
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Commission deducted</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor(abs($summary['commission']) - $summary['commission_reversal']), ENT_QUOTES, 'UTF-8') ?></div></div></div>
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Refunds</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor(abs($summary['refund'])), ENT_QUOTES, 'UTF-8') ?></div></div></div>
    </div>
    <div class="row">
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Adjustments</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor($summary['adjustment']), ENT_QUOTES, 'UTF-8') ?></div></div></div>
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Already paid</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor(abs($summary['payout'])), ENT_QUOTES, 'UTF-8') ?></div></div></div>
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Outstanding balance</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor($summary['balance']), ENT_QUOTES, 'UTF-8') ?></div></div></div>
    </div>
    <h3>Payout history</h3>
    <table class="table">
        <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (empty($payouts)) { ?><tr><td colspan="5">No payouts yet.</td></tr><?php } ?>
        <?php foreach ($payouts as $payout) { ?>
            <tr>
                <td><?= htmlspecialchars($payout['paid_on'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>Rs. <?= htmlspecialchars(Order_quote::formatMinor($payout['amount_minor']), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($payout['method'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($payout['reference_code'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($payout['status'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
