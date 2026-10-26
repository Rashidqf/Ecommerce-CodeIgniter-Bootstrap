<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<main class="buyora-container buyora-public">
    <div class="buyora-split">
        <section class="buyora-panel">
            <h1><?= htmlspecialchars(lang('contact_us'), ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="buyora-copy"><?= isset($contactspage) ? html_entity_decode($contactspage) : '' ?></div>
            <p class="buyora-copy" style="line-height:1.7;color:#4b5563;">Have a question about an active order, seller inquiries, delivery timelines, or payment verification? Our dedicated BUYORA customer support team is available Monday through Saturday from 9:00 AM to 9:00 PM PKT to assist you with prompt, friendly, and reliable solutions.</p>
            <p class="buyora-copy"><i class="fa fa-phone" aria-hidden="true"></i> <strong>Cell:</strong> 
            <?php
            $contactPhones = array_filter(array_map('trim', explode(',', isset($footerContactPhone) && $footerContactPhone !== '' ? $footerContactPhone : '0333-6108869, 0308-8503190')));
            $phoneLinks = array();
            foreach ($contactPhones as $cp) {
                $cleanCp = preg_replace('/[^0-9+]/', '', $cp);
                $phoneLinks[] = '<a href="tel:' . htmlspecialchars($cleanCp, ENT_QUOTES, 'UTF-8') . '" style="color:inherit;text-decoration:none;font-weight:600;">' . htmlspecialchars($cp, ENT_QUOTES, 'UTF-8') . '</a>';
            }
            echo implode(', ', $phoneLinks);
            ?>
            </p>
            <p class="buyora-copy"><i class="fa fa-envelope" aria-hidden="true"></i> <strong>Email:</strong> <a href="mailto:<?= htmlspecialchars(isset($footerContactEmail) && $footerContactEmail !== '' ? $footerContactEmail : 'biolabtraders@gmail.com', ENT_QUOTES, 'UTF-8') ?>" style="color:inherit;text-decoration:none;font-weight:600;"><?= htmlspecialchars(isset($footerContactEmail) && $footerContactEmail !== '' ? $footerContactEmail : 'biolabtraders@gmail.com', ENT_QUOTES, 'UTF-8') ?></a></p>
            <p class="buyora-copy"><i class="fa fa-map-marker" aria-hidden="true"></i> <strong>Headquarters:</strong> <?= htmlspecialchars(isset($footerContactAddr) && $footerContactAddr !== '' ? $footerContactAddr : 'Office No. 5, 1st Floor, Karim Centre, 16-Edward Road, Lahore, Pakistan', ENT_QUOTES, 'UTF-8') ?></p>
            <div style="margin-top:20px;padding:16px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;font-size:13px;line-height:1.6;color:#4b5563;">
                <strong>Frequently Asked Support Topics:</strong>
                <ul style="margin:8px 0 0 16px;padding:0;">
                    <li><strong>Order Tracking:</strong> Check real-time shipping progress via your order confirmation or account dashboard.</li>
                    <li><strong>Returns &amp; Refunds:</strong> Requests submitted within 7 days of package delivery are reviewed within 48 business hours.</li>
                    <li><strong>Merchant Partnerships:</strong> Interested in opening an official storefront? Reach our vendor onboarding department.</li>
                </ul>
            </div>
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
