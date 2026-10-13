<div>
    <h1><i class="fa fa-balance-scale" aria-hidden="true"></i> Vendor balances</h1>
    <hr>
    <?php if ($this->session->flashdata('result')) { ?><div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('result'), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
    <?php if ($this->session->flashdata('error')) { ?><div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr><th>Vendor</th><th>Payable balance</th></tr></thead>
            <tbody>
            <?php foreach ($balances as $balance) { ?>
                <tr>
                    <td><?= htmlspecialchars($balance['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>Rs. <?= htmlspecialchars(Order_quote::formatMinor($balance['balance']), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <h3>Bank transfers waiting for confirmation</h3>
    <?php if (empty($bankPayments)) { ?><p>No unpaid bank transfers.</p><?php } ?>
    <?php foreach ($bankPayments as $payment) { ?>
        <form method="POST" class="form-inline" style="margin-bottom:8px;">
            <input type="hidden" name="payment_id" value="<?= (int) $payment['id'] ?>">
            Order <?= (int) $payment['order_number'] ?>, Rs. <?= htmlspecialchars(Order_quote::formatMinor($payment['amount_minor']), ENT_QUOTES, 'UTF-8') ?>
            <button class="btn btn-primary btn-xs" name="mark_bank_paid" value="1" type="submit">Mark money received</button>
        </form>
    <?php } ?>
    <h3>Record a refund</h3>
    <form method="POST" class="form-inline">
        <select class="form-control" name="order_item_id">
            <?php foreach ($items as $item) { ?>
                <option value="<?= (int) $item['id'] ?>"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?> / order <?= (int) $item['order_number'] ?></option>
            <?php } ?>
        </select>
        <input class="form-control" name="refund_amount" placeholder="Amount in Rs." required>
        <input class="form-control" name="reference" placeholder="Refund reference" required>
        <button class="btn btn-default" name="refund_item" value="1" type="submit">Record refund</button>
    </form>
    <h3>Record an adjustment</h3>
    <form method="POST" class="form-inline">
        <select class="form-control" name="vendor_id">
            <?php foreach ($balances as $balance) { ?><option value="<?= (int) $balance['id'] ?>"><?= htmlspecialchars($balance['name'], ENT_QUOTES, 'UTF-8') ?></option><?php } ?>
        </select>
        <input class="form-control" name="amount" placeholder="Rs. Use a minus for a deduction" required>
        <input class="form-control" name="reference" placeholder="Reference" required>
        <input class="form-control" name="note" placeholder="Note">
        <button class="btn btn-default" name="adjust_balance" value="1" type="submit">Record adjustment</button>
    </form>
</div>
