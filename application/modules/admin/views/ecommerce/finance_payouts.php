<div>
    <h1><i class="fa fa-money" aria-hidden="true"></i> Manual vendor payouts</h1>
    <hr>
    <?php if ($this->session->flashdata('result')) { ?><div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('result'), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
    <?php if ($this->session->flashdata('error')) { ?><div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
    <p>Recording a payout means you already transferred the money yourself. Buyora does not send it through Safepay.</p>
    <form method="POST">
        <div class="row">
            <div class="col-sm-4 form-group">
                <label>Vendor</label>
                <select class="form-control" name="vendor_id">
                    <?php foreach ($balances as $balance) { ?>
                        <option value="<?= (int) $balance['id'] ?>"><?= htmlspecialchars($balance['name'], ENT_QUOTES, 'UTF-8') ?> (Rs. <?= htmlspecialchars(Order_quote::formatMinor($balance['balance']), ENT_QUOTES, 'UTF-8') ?>)</option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-2 form-group"><label>Amount (Rs.)</label><input class="form-control" name="amount" required></div>
            <div class="col-sm-2 form-group">
                <label>Method</label>
                <select class="form-control" name="method"><option value="bank">Bank transfer</option><option value="cash">Cash</option><option value="other">Other</option></select>
            </div>
            <div class="col-sm-2 form-group"><label>Date</label><input class="form-control" type="date" name="paid_on" required></div>
            <div class="col-sm-2 form-group"><label>Reference</label><input class="form-control" name="reference" required></div>
        </div>
        <div class="form-group"><label>Notes</label><input class="form-control" name="notes"></div>
        <button class="btn btn-primary" type="submit">Record payout</button>
    </form>
    <hr>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead><tr><th>Vendor</th><th>Amount</th><th>Method</th><th>Date</th><th>Reference</th><th>Status</th><th>Admin</th></tr></thead>
            <tbody>
            <?php if (empty($payouts)) { ?><tr><td colspan="7">No payouts recorded.</td></tr><?php } ?>
            <?php foreach ($payouts as $payout) { ?>
                <tr>
                    <td><?= htmlspecialchars($payout['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>Rs. <?= htmlspecialchars(Order_quote::formatMinor($payout['amount_minor']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($payout['method'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($payout['paid_on'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($payout['reference_code'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($payout['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($payout['admin_user'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
