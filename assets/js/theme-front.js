if (typeof $ == 'undefined') {
    var $ = jQuery;
}

$(window).on('load', () => {
    setHeaderOnScroll();
    toggleCartItemData();
    setMiniCartButtonText();
    setItemGallery();

    $('body').on('updated_checkout updated_cart_totals updated_wc_div', () => {
        toggleCartItemData();
        setItemGallery();
    });
});

function setHeaderOnScroll() {
    const $header = $('header');
}

function toggleCartItemData() {
    const $toggleButton = $('.custom-item-data-toggle');
    // const $content = $container.find('.content');

    $toggleButton.on('click', function (e) {
        e.preventDefault();
        const $container = $(this).closest('.custom-item-data-container');
        $container.toggleClass('active');
    });
}


function setItemGallery() {
    const $thumbnailGallery = $('.cart-item-thumbnail-gallery');
    if (!$thumbnailGallery.length) return;

    $thumbnailGallery.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        rtl: true,
        variableWidth: false,
        arrows: false,
        dots: true,
        adaptiveHeight: true,

    });
}

function setMiniCartButtonText() {
    const targetElement = $('.widget_shopping_cart_content');
    targetElement.find('a.elementor-button--view-cart').text('סל קניות');
}