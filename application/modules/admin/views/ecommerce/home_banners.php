<div id="home-banners">
    <h1><i class="fa fa-picture-o" aria-hidden="true"></i> Homepage banners</h1>
    <hr>
    <?php if ($this->session->flashdata('result')) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('result'), ENT_QUOTES, 'UTF-8') ?></div>
    <?php } ?>
    <?php if ($this->session->flashdata('error')) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8') ?></div>
    <?php } ?>
    <p>The first visible hero banner is used at the top of the store. Visible promotional banners fill the existing promotion cards.</p>
    <div class="panel panel-default">
        <div class="panel-heading">Add a banner</div>
        <div class="panel-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-2 form-group">
                        <label>Placement</label>
                        <select class="form-control" name="placement">
                            <option value="hero">Hero</option>
                            <option value="promo">Promotion</option>
                        </select>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Image description</label>
                        <input class="form-control" type="text" name="alt_text" maxlength="255" required>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Shop link</label>
                        <input class="form-control" type="text" name="target_url" value="#featured" required>
                    </div>
                    <div class="col-sm-1 form-group">
                        <label>Order</label>
                        <input class="form-control" type="number" name="position" min="0" value="1" required>
                    </div>
                    <div class="col-sm-1 form-group">
                        <label>Visible</label>
                        <select class="form-control" name="visible"><option value="1">Yes</option><option value="0">No</option></select>
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>Desktop image</label>
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label>Mobile image (optional)</label>
                        <input type="file" name="mobile_image" accept=".jpg,.jpeg,.png,.webp">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>&nbsp;</label><br>
                        <button class="btn btn-primary" type="submit">Add banner</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($banners)) { ?>
        <div class="alert alert-info">No homepage banners have been added.</div>
    <?php } else { ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Image</th><th>Placement</th><th>Description</th><th>Destination</th><th>Order</th><th>Visible</th><th>New image (optional)</th><th>Save</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($banners as $banner) { $formId = 'banner-form-' . (int) $banner['id']; ?>
                        <form id="<?= $formId ?>" method="POST" enctype="multipart/form-data"></form>
                        <tr>
                            <td><img src="<?= base_url($banner['image']) ?>" alt="" style="max-width:150px;max-height:70px;"></td>
                            <td>
                                <input type="hidden" form="<?= $formId ?>" name="banner_id" value="<?= (int) $banner['id'] ?>">
                                <select class="form-control" form="<?= $formId ?>" name="placement">
                                    <option value="hero" <?= $banner['placement'] === 'hero' ? 'selected' : '' ?>>Hero</option>
                                    <option value="promo" <?= $banner['placement'] === 'promo' ? 'selected' : '' ?>>Promotion</option>
                                </select>
                            </td>
                            <td><input class="form-control" form="<?= $formId ?>" name="alt_text" maxlength="255" value="<?= htmlspecialchars($banner['alt_text'], ENT_QUOTES, 'UTF-8') ?>" required></td>
                            <td><input class="form-control" form="<?= $formId ?>" name="target_url" value="<?= htmlspecialchars($banner['target_url'], ENT_QUOTES, 'UTF-8') ?>" required></td>
                            <td><input class="form-control" form="<?= $formId ?>" type="number" min="0" name="position" value="<?= (int) $banner['position'] ?>" required></td>
                            <td>
                                <select class="form-control" form="<?= $formId ?>" name="visible">
                                    <option value="1" <?= (int) $banner['visible'] === 1 ? 'selected' : '' ?>>Yes</option>
                                    <option value="0" <?= (int) $banner['visible'] === 0 ? 'selected' : '' ?>>No</option>
                                </select>
                            </td>
                            <td>
                                <label>Desktop</label><input type="file" form="<?= $formId ?>" name="image" accept=".jpg,.jpeg,.png,.webp">
                                <label>Mobile</label><input type="file" form="<?= $formId ?>" name="mobile_image" accept=".jpg,.jpeg,.png,.webp">
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs" type="submit" form="<?= $formId ?>">Save</button>
                                <button class="btn btn-danger btn-xs" type="submit" form="<?= $formId ?>" name="delete_banner" value="1" onclick="return confirm('Remove this banner?')">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>
