if(typeof $ == 'undefined') {
    var $ = jQuery;
}

$(window).on('load', () => {
    setHeaderOnScroll();
    toggleCartItemData();
});

function setHeaderOnScroll() {
    const $header= $('header');
}

function toggleCartItemData() {
    const $toggleButton = $('.custom-item-data-toggle');
    // const $content = $container.find('.content');

    $toggleButton.on('click', function(e) {
        e.preventDefault();
        const $container = $(this).closest('.custom-item-data-container');
        $container.toggleClass('active');
    });
}