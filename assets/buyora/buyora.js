(function () {
    var menuButton = document.querySelector('.mobile-menu-toggle');
    var navigation = document.getElementById('primary-navigation');

    if (menuButton && navigation) {
        menuButton.addEventListener('click', function () {
            var isOpen = menuButton.getAttribute('aria-expanded') === 'true';
            menuButton.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            menuButton.setAttribute('aria-label', isOpen ? 'Open menu' : 'Close menu');
            navigation.classList.toggle('is-open', !isOpen);
        });
    }

    var tabs = document.querySelectorAll('.product-tab');
    var products = document.querySelectorAll('.product-card');
    function showCollection(tab) {
        var collection = tab.getAttribute('data-collection');
        Array.prototype.forEach.call(tabs, function (item) {
            var selected = item === tab;
            item.classList.toggle('is-active', selected);
            item.setAttribute('aria-selected', selected ? 'true' : 'false');
        });
        Array.prototype.forEach.call(products, function (product) {
            var collections = (product.getAttribute('data-product-collection') || '').split(' ');
            product.hidden = collections.indexOf(collection) === -1;
        });
    }

    Array.prototype.forEach.call(tabs, function (tab) {
        tab.addEventListener('click', function () {
            showCollection(tab);
        });
    });
    Array.prototype.forEach.call(tabs, function (tab) {
        if (tab.getAttribute('aria-selected') === 'true') {
            showCollection(tab);
        }
    });

    Array.prototype.forEach.call(document.querySelectorAll('.product-favorite'), function (button) {
        button.addEventListener('click', function () {
            var isFavorite = button.getAttribute('aria-pressed') === 'true';
            button.setAttribute('aria-pressed', isFavorite ? 'false' : 'true');
            button.innerHTML = isFavorite
                ? '<i class="fa fa-heart-o" aria-hidden="true"></i>'
                : '<i class="fa fa-heart" aria-hidden="true"></i>';
        });
    });

}());
