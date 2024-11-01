($ => {
    $(window).on('load', () => {
        setTableKeys();
        setItemGallery();
        $('body').on('updated_checkout updated_cart_totals updated_wc_div', () => {
            const interval = setInterval(() => {
                console.log('checking');
                if (!$.active) {
                    clearInterval(interval);
                    setTableKeys();
                    setItemGallery();
                }
            }, 100);
        });
    });

    function setTableKeys() {
        const $labels = $('table.cart tr.cart_item dt');

        $labels.each(function () {
            $(this).next().andSelf().wrapAll('<div class="row-wrapper"/>');
        });

        $('table.cart tr.cart_item dd.variation-vegan,table.cart tr.cart_item dd.variation-gluten').remove();
        $('table.cart tr.cart_item dt.variation-vegan,table.cart tr.cart_item dt.variation-gluten').each(function () {
            const currentText = $(this).text(),
                formattedText = currentText.replace(':', '');
            $(this).text(formattedText);
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
})(jQuery);