<?php if ($this->session->flashdata('update_vend_err')) { ?>
    <div class="alert alert-danger"><?= implode('<br>', $this->session->flashdata('update_vend_err')) ?></div>
<?php } ?>
<?php if ($this->session->flashdata('update_vend_details')) { ?>
    <div class="alert alert-success"><?= $this->session->flashdata('update_vend_details') ?></div>
<?php } ?>

<div class="content">
    <?php if (empty($banking['iban']) && empty($banking['account_number']) && empty($banking['raast_id'])) { ?>
        <div class="alert alert-warning" role="alert" style="border-left: 5px solid #f59e0b; font-size: 14px;">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            <strong>Payout Setup Incomplete:</strong> Please provide your Pakistani Bank IBAN or Raast ID below so BUYORA marketplace can transfer your earnings.
        </div>
    <?php } ?>

    <?php if (!empty($summary)) { ?>
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-sm-4">
                <div class="panel panel-default" style="border-top: 3px solid #10b981;">
                    <div class="panel-heading" style="font-weight: 600; font-size: 13px; text-transform: uppercase; color: #047857;">Total Sales</div>
                    <div class="panel-body" style="font-size: 22px; font-weight: 700; color: #0f172a;">Rs. <?= htmlspecialchars(Order_quote::formatMinor($summary['sale']), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-default" style="border-top: 3px solid #6366f1;">
                    <div class="panel-heading" style="font-weight: 600; font-size: 13px; text-transform: uppercase; color: #4338ca;">Already Paid Out</div>
                    <div class="panel-body" style="font-size: 22px; font-weight: 700; color: #0f172a;">Rs. <?= htmlspecialchars(Order_quote::formatMinor(abs($summary['payout'])), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-default" style="border-top: 3px solid #f97316;">
                    <div class="panel-heading" style="font-weight: 600; font-size: 13px; text-transform: uppercase; color: #c2410c;">Outstanding Payable Balance</div>
                    <div class="panel-body" style="font-size: 22px; font-weight: 700; color: #c2410c;">Rs. <?= htmlspecialchars(Order_quote::formatMinor($summary['balance']), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Bank Account & Payout Disbursement Details Card -->
    <div class="panel panel-default" style="border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div class="panel-heading" style="background: #f8fafc; font-weight: 700; font-size: 16px; padding: 15px 20px; border-bottom: 1px solid #e2e8f0;">
            <i class="fa fa-university" style="color: #c8460a; margin-right: 8px;"></i> Bank Account & Payout Details
            <span style="float: right; font-size: 12px; font-weight: normal; color: #64748b;">Funds from Bank Alfalah customer payments will be transferred here</span>
        </div>
        <div class="panel-body" style="padding: 20px;">
            <form method="POST" action="<?= LANG_URL . '/vendor/me' ?>">
                <input type="hidden" name="return_url" value="<?= LANG_URL . '/vendor/me' ?>">
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label style="font-weight: 600; font-size: 13px;">Bank Name *</label>
                        <input type="text" class="form-control" name="bank_name" placeholder="e.g. Bank Alfalah, Meezan Bank, HBL, JazzCash" value="<?= htmlspecialchars(!empty($banking['bank_name']) ? $banking['bank_name'] : '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label style="font-weight: 600; font-size: 13px;">Account Title *</label>
                        <input type="text" class="form-control" name="account_title" placeholder="Name as registered on bank account" value="<?= htmlspecialchars(!empty($banking['account_title']) ? $banking['account_title'] : '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label style="font-weight: 600; font-size: 13px;">Account Number</label>
                        <input type="text" class="form-control" name="account_number" placeholder="e.g. 0123456789" value="<?= htmlspecialchars(!empty($banking['account_number']) ? $banking['account_number'] : '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label style="font-weight: 600; font-size: 13px;">IBAN (24 Digits) *</label>
                        <input type="text" class="form-control" name="iban" placeholder="PK36ALFH0000000000000000" maxlength="34" value="<?= htmlspecialchars(!empty($banking['iban']) ? $banking['iban'] : '', ENT_QUOTES, 'UTF-8') ?>" style="text-transform: uppercase;">
                        <small class="text-muted">Required for 1LINK IBFT bank-to-bank settlement.</small>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label style="font-weight: 600; font-size: 13px;">Raast ID (Optional)</label>
                        <input type="text" class="form-control" name="raast_id" placeholder="e.g. 03001234567" value="<?= htmlspecialchars(!empty($banking['raast_id']) ? $banking['raast_id'] : '', ENT_QUOTES, 'UTF-8') ?>">
                        <small class="text-muted">Registered Raast mobile number for instant disbursement.</small>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label style="font-weight: 600; font-size: 13px;">CNIC Number (Optional)</label>
                        <input type="text" class="form-control" name="cnic" placeholder="e.g. 35202-1234567-1" value="<?= htmlspecialchars(!empty($banking['cnic']) ? $banking['cnic'] : '', ENT_QUOTES, 'UTF-8') ?>">
                        <small class="text-muted">Tax & KYC compliance.</small>
                    </div>
                </div>
                <div style="margin-top: 10px;">
                    <button type="submit" name="saveBankingDetails" class="btn btn-primary" style="background-color: #c8460a; border-color: #c8460a; padding: 8px 24px; font-weight: 600;">
                        <i class="fa fa-save"></i> Save Bank Details
                    </button>
                    <a href="<?= LANG_URL . '/vendor/finance' ?>" class="btn btn-default" style="margin-left: 10px;">
                        <i class="fa fa-history"></i> View Payout History
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= base_url('assets/highcharts/highcharts.js') ?>"></script>
    <div id="container-by-month" style="min-width: 310px; height: 360px; margin: 20px auto 0 auto;"></div>
    <script>
        $(function () {
            Highcharts.chart('container-by-month', {
                title: { text: 'Monthly Orders', x: -20 },
                subtitle: { text: 'Source: Orders table', x: -20 },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                yAxis: {
                    title: { text: 'Orders' },
                    plotLines: [{ value: 0, width: 1, color: '#808080' }]
                },
                tooltip: { valueSuffix: ' Orders' },
                legend: { layout: 'vertical', align: 'right', verticalAlign: 'middle', borderWidth: 0 },
                series: [
                <?php if (!empty($ordersByMonth['years'])) { foreach ($ordersByMonth['years'] as $year) { ?>
                    {
                        name: '<?= $year ?>',
                        data: [<?= implode(',', $ordersByMonth['orders'][$year]) ?>]
                    },
                <?php } } ?>
                ]
            });
        });
    </script>
</div>
