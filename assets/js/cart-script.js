($ => {
    $(window).on('load', () => {
        setTableKeys();
        $('body').on('updated_checkout updated_cart_totals updated_wc_div', () => {
            const interval = setInterval(() => {
                if (!$.active) {
                    clearInterval(interval);
                    setTableKeys();
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
})(jQuery);