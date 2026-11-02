<div class="content">
    <div class="row">
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Eligible sales</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor($summary['sale']), ENT_QUOTES, 'UTF-8') ?></div></div></div>
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Commission deducted</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor(abs($summary['commission']) - $summary['commission_reversal']), ENT_QUOTES, 'UTF-8') ?></div></div></div>
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Refunds</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor(abs($summary['refund'])), ENT_QUOTES, 'UTF-8') ?></div></div></div>
    </div>
    <div class="row">
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Adjustments</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor($summary['adjustment']), ENT_QUOTES, 'UTF-8') ?></div></div></div>
        <div class="col-sm-4"><div class="panel panel-default"><div class="panel-heading">Already paid</div><div class="panel-body">Rs. <?= htmlspecialchars(Order_quote::formatMinor(abs($summary['payout'])), ENT_QUOTES, 'UTF-8') ?></div></div></div>
        <div class="col-sm-4"><div class="panel panel-default" style="border-top: 3px solid #c8460a;"><div class="panel-heading" style="color: #c8460a; font-weight: 700;">Outstanding payable balance</div><div class="panel-body" style="font-size: 22px; font-weight: 700; color: #c8460a;">Rs. <?= htmlspecialchars(Order_quote::formatMinor($summary['balance']), ENT_QUOTES, 'UTF-8') ?></div></div></div>
    </div>

    <!-- Active Bank Account for Payouts -->
    <div class="panel panel-default" style="border-radius: 8px;">
        <div class="panel-heading" style="background: #f8fafc; font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fa fa-university" style="color: #c8460a;"></i> Active Payout Bank Account</span>
            <a href="<?= LANG_URL . '/vendor/me' ?>" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i> Edit Details</a>
        </div>
        <div class="panel-body">
            <?php if (!empty($banking['bank_name']) || !empty($banking['iban']) || !empty($banking['raast_id'])) { ?>
                <div class="row">
                    <div class="col-sm-3"><strong>Bank:</strong> <?= htmlspecialchars(!empty($banking['bank_name']) ? $banking['bank_name'] : 'Not set', ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="col-sm-3"><strong>Account Title:</strong> <?= htmlspecialchars(!empty($banking['account_title']) ? $banking['account_title'] : 'Not set', ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="col-sm-3"><strong>IBAN:</strong> <code style="font-size: 13px;"><?= htmlspecialchars(!empty($banking['iban']) ? $banking['iban'] : (!empty($banking['account_number']) ? $banking['account_number'] : 'Not set'), ENT_QUOTES, 'UTF-8') ?></code></div>
                    <div class="col-sm-3"><strong>Raast ID:</strong> <?= htmlspecialchars(!empty($banking['raast_id']) ? $banking['raast_id'] : 'Not set', ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning" style="margin-bottom: 0;">
                    No bank account linked yet. <a href="<?= LANG_URL . '/vendor/me' ?>" class="alert-link">Click here to add your IBAN / Raast ID</a> to receive payouts.
                </div>
            <?php } ?>
        </div>
    </div>

    <h3>Payout history</h3>
    <table class="table table-striped table-bordered">
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
