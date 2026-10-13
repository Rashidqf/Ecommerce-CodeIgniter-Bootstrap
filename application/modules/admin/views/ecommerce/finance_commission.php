<div>
    <h1><i class="fa fa-percent" aria-hidden="true"></i> Commission</h1>
    <hr>
    <?php if ($this->session->flashdata('result')) { ?><div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('result'), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
    <?php if ($this->session->flashdata('error')) { ?><div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8') ?></div><?php } ?>
    <p>The marketplace keeps this percentage of each vendor product line after its share of the discount. Shipping stays with Buyora. Marketplace-owned products are not paid out to a vendor. The rate is saved on each new order item. It is not applied to older orders.</p>
    <p>Current rate: <strong><?= htmlspecialchars($rate, ENT_QUOTES, 'UTF-8') ?>%</strong>. Zero means the vendor receives the full line until you set a rate.</p>
    <form method="POST" class="form-inline">
        <div class="form-group">
            <label for="commission-rate">Commission percent</label>
            <input id="commission-rate" class="form-control" name="commission_rate_percent" value="<?= htmlspecialchars($rate, ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <button class="btn btn-primary" type="submit">Save commission</button>
    </form>
</div>
