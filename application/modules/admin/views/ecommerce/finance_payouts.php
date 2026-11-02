<div>
    <h1><i class="fa fa-money" aria-hidden="true"></i> Vendor Payouts & Disbursements</h1>
    <hr>
    <?php if ($this->session->flashdata('result')) { ?><div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('result'), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
    <?php if ($this->session->flashdata('error')) { ?><div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
    
    <div class="alert alert-info" style="font-size: 13px;">
        <i class="fa fa-info-circle"></i> <strong>Disbursement Workflow:</strong> Customer payments via Bank Alfalah settle into your platform's master bank account. Use the vendor's IBAN / Raast ID below to disburse their net earnings via Bank Alfalah Corporate Internet Banking, 1LINK IBFT, or Raast, then record the transaction reference here.
    </div>

    <form method="POST" id="payoutForm">
        <div class="row">
            <div class="col-sm-4 form-group">
                <label>Select Vendor</label>
                <select class="form-control" name="vendor_id" id="vendorSelect">
                    <option value="">-- Choose a vendor --</option>
                    <?php foreach ($balances as $balance) { ?>
                        <option value="<?= (int) $balance['id'] ?>"
                            data-bank="<?= htmlspecialchars(!empty($balance['bank_name']) ? $balance['bank_name'] : '', ENT_QUOTES, 'UTF-8') ?>"
                            data-title="<?= htmlspecialchars(!empty($balance['account_title']) ? $balance['account_title'] : '', ENT_QUOTES, 'UTF-8') ?>"
                            data-account="<?= htmlspecialchars(!empty($balance['account_number']) ? $balance['account_number'] : '', ENT_QUOTES, 'UTF-8') ?>"
                            data-iban="<?= htmlspecialchars(!empty($balance['iban']) ? $balance['iban'] : '', ENT_QUOTES, 'UTF-8') ?>"
                            data-raast="<?= htmlspecialchars(!empty($balance['raast_id']) ? $balance['raast_id'] : '', ENT_QUOTES, 'UTF-8') ?>"
                            data-balance="<?= htmlspecialchars(Order_quote::formatMinor($balance['balance']), ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($balance['name'], ENT_QUOTES, 'UTF-8') ?> (Rs. <?= htmlspecialchars(Order_quote::formatMinor($balance['balance']), ENT_QUOTES, 'UTF-8') ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-2 form-group"><label>Amount (Rs.)</label><input class="form-control" name="amount" id="payoutAmount" placeholder="0.00" required></div>
            <div class="col-sm-2 form-group">
                <label>Payment Method</label>
                <select class="form-control" name="method">
                    <option value="bank">1LINK / Bank Transfer</option>
                    <option value="other">Raast (Instant P2B/B2P)</option>
                    <option value="cash">Cash / Cheque</option>
                </select>
            </div>
            <div class="col-sm-2 form-group"><label>Disbursement Date</label><input class="form-control" type="date" name="paid_on" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-sm-2 form-group"><label>Bank Reference / STAN</label><input class="form-control" name="reference" placeholder="e.g. FT261103001" required></div>
        </div>

        <!-- Dynamic Vendor Banking Profile Card -->
        <div id="vendorBankBox" class="panel panel-default" style="display: none; background: #fdfdfd; border-left: 4px solid #c8460a; margin-bottom: 20px;">
            <div class="panel-body" style="padding: 15px 20px;">
                <h4 style="margin-top: 0; font-size: 15px; font-weight: 700; color: #1e293b;">
                    <i class="fa fa-university text-danger"></i> Vendor Payout Destination
                </h4>
                <div class="row" style="font-size: 13px;">
                    <div class="col-sm-3"><strong>Bank:</strong> <span id="dispBank">-</span></div>
                    <div class="col-sm-3"><strong>Title:</strong> <span id="dispTitle">-</span></div>
                    <div class="col-sm-3">
                        <strong>IBAN:</strong> <code id="dispIban">-</code>
                        <button type="button" class="btn btn-default btn-xs" id="copyIbanBtn" title="Copy IBAN"><i class="fa fa-copy"></i></button>
                    </div>
                    <div class="col-sm-3">
                        <strong>Raast ID:</strong> <code id="dispRaast">-</code>
                        <button type="button" class="btn btn-default btn-xs" id="copyRaastBtn" title="Copy Raast"><i class="fa fa-copy"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group"><label>Internal Notes</label><input class="form-control" name="notes" placeholder="e.g. Weekly settlement transferred via Bank Alfalah NetBanking"></div>
        <button class="btn btn-primary" type="submit" style="background-color: #c8460a; border-color: #c8460a;"><i class="fa fa-check"></i> Record Payout in Ledger</button>
    </form>
    <hr>
    <h3>Payout History</h3>
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
                    <td><code><?= htmlspecialchars($payout['reference_code'], ENT_QUOTES, 'UTF-8') ?></code></td>
                    <td><span class="label label-success"><?= htmlspecialchars($payout['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= htmlspecialchars($payout['admin_user'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('vendorSelect');
    var box = document.getElementById('vendorBankBox');
    var dispBank = document.getElementById('dispBank');
    var dispTitle = document.getElementById('dispTitle');
    var dispIban = document.getElementById('dispIban');
    var dispRaast = document.getElementById('dispRaast');
    var amount = document.getElementById('payoutAmount');

    function updateDetails() {
        var opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            box.style.display = 'none';
            return;
        }
        var bank = opt.getAttribute('data-bank') || 'Not provided';
        var title = opt.getAttribute('data-title') || 'Not provided';
        var iban = opt.getAttribute('data-iban') || opt.getAttribute('data-account') || 'Not provided';
        var raast = opt.getAttribute('data-raast') || 'Not provided';
        var bal = opt.getAttribute('data-balance') || '0.00';

        dispBank.innerText = bank;
        dispTitle.innerText = title;
        dispIban.innerText = iban;
        dispRaast.innerText = raast;
        box.style.display = 'block';

        if (bal && parseFloat(bal) > 0 && (!amount.value || amount.value === '0.00')) {
            amount.value = bal;
        }
    }

    select.addEventListener('change', updateDetails);
    if (select.value) updateDetails();

    document.getElementById('copyIbanBtn').addEventListener('click', function() {
        var val = dispIban.innerText;
        if (val && val !== 'Not provided') {
            navigator.clipboard.writeText(val);
            alert('IBAN copied: ' + val);
        }
    });

    document.getElementById('copyRaastBtn').addEventListener('click', function() {
        var val = dispRaast.innerText;
        if (val && val !== 'Not provided') {
            navigator.clipboard.writeText(val);
            alert('Raast ID copied: ' + val);
        }
    });
});
</script>
