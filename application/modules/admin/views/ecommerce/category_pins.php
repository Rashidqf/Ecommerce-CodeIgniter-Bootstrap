<div id="category-pins">
    <h1><i class="fa fa-thumb-tack" aria-hidden="true"></i> Pin categories</h1>
    <hr>
    <?php if ($this->session->flashdata('result')) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('result'), ENT_QUOTES, 'UTF-8') ?></div>
    <?php } ?>
    <p>Choose existing categories for the storefront. A multi-vendor shop can have many categories, so only the pinned ones appear as links in the header and footer. The All Categories dropdown always lists every category.</p>
    <?php if (empty($categories)) { ?>
        <div class="alert alert-info">Add shop categories first, then pin them here.</div>
    <?php } else { ?>
        <form method="POST">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Parent</th>
                            <th class="text-center">Show in header</th>
                            <th class="text-center">Show in footer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $id => $category) {
                            $label = '';
                            foreach ($category['info'] as $info) {
                                $name = isset($info['name']) ? $info['name'] : '';
                                if ($label === '' || (isset($info['abbr']) && $info['abbr'] === MY_DEFAULT_LANGUAGE_ABBR)) {
                                    $label = $name;
                                }
                            }
                            $parent = isset($category['sub'][0]) && $category['sub'][0] !== null ? $category['sub'][0] : '';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= $parent === '' ? 'Top level' : htmlspecialchars($parent, ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-center">
                                    <input type="checkbox" name="pin_header[]" value="<?= (int) $id ?>" <?= in_array((int) $id, $headerPins, true) ? 'checked' : '' ?> aria-label="Show <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> in the header">
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="pin_footer[]" value="<?= (int) $id ?>" <?= in_array((int) $id, $footerPins, true) ? 'checked' : '' ?> aria-label="Show <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> in the footer">
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-primary" type="submit">Save pinned categories</button>
        </form>
    <?php } ?>
</div>
