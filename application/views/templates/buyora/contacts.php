<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<main class="buyora-container buyora-public">
    <div class="buyora-split">
        <section class="buyora-panel">
            <h1><?= htmlspecialchars(lang('contact_us'), ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="buyora-copy"><?= isset($contactspage) ? html_entity_decode($contactspage) : '' ?></div>
            <p class="buyora-copy"><i class="fa fa-phone" aria-hidden="true"></i> <?= htmlspecialchars(isset($footerContactPhone) && $footerContactPhone !== '' ? $footerContactPhone : '+92 300 1234567', ENT_QUOTES, 'UTF-8') ?></p>
            <p class="buyora-copy"><i class="fa fa-envelope" aria-hidden="true"></i> <?= htmlspecialchars(isset($footerContactEmail) && $footerContactEmail !== '' ? $footerContactEmail : 'support@shop.dev', ENT_QUOTES, 'UTF-8') ?></p>
            <p class="buyora-copy"><i class="fa fa-map-marker" aria-hidden="true"></i> <?= htmlspecialchars(isset($footerContactAddr) && $footerContactAddr !== '' ? $footerContactAddr : 'Karachi, Pakistan', ENT_QUOTES, 'UTF-8') ?></p>
        </section>
        <section class="buyora-panel">
            <?php if ($this->session->flashdata('resultSend')) { ?>
                <div class="buyora-notice" role="status"><?= htmlspecialchars($this->session->flashdata('resultSend'), ENT_QUOTES, 'UTF-8') ?></div>
            <?php } ?>
            <form class="buyora-form" method="POST" action="">
                <label for="contact-name"><?= htmlspecialchars(lang('name'), ENT_QUOTES, 'UTF-8') ?></label>
                <input id="contact-name" type="text" name="name" placeholder="Enter name" required>
                <label for="contact-email"><?= htmlspecialchars(lang('email_address'), ENT_QUOTES, 'UTF-8') ?></label>
                <input id="contact-email" type="email" name="email" placeholder="Enter email" required>
                <label for="contact-subject"><?= htmlspecialchars(lang('subject'), ENT_QUOTES, 'UTF-8') ?></label>
                <input id="contact-subject" type="text" name="subject">
                <label for="contact-message"><?= htmlspecialchars(lang('message'), ENT_QUOTES, 'UTF-8') ?></label>
                <textarea id="contact-message" name="message" rows="8" placeholder="<?= htmlspecialchars(lang('message'), ENT_QUOTES, 'UTF-8') ?>" required></textarea>
                <button class="buyora-button buyora-button-primary" type="submit"><?= htmlspecialchars(lang('send_message'), ENT_QUOTES, 'UTF-8') ?></button>
            </form>
        </section>
    </div>
    <?php if (isset($googleApi, $googleMaps) && trim($googleApi) != null && trim($googleMaps) != null) {
        $coordinates = explode(',', $googleMaps); ?>
        <div id="map" style="height:280px;margin-top:18px;border-radius:16px;overflow:hidden;"></div>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($googleApi, ENT_QUOTES, 'UTF-8') ?>"></script>
        <script>
            function initialize() {
                var myLatlng = new google.maps.LatLng(<?= (float) $coordinates[0] ?>, <?= (float) $coordinates[1] ?>);
                var map = new google.maps.Map(document.getElementById('map'), {zoom: 10, center: myLatlng});
                new google.maps.Marker({position: myLatlng, map: map});
            }
            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    <?php } ?>
</main>
