($ => {
    $(window).on('load', () => {
        setOrderRecipientsForm();
        setDeliveryForm();
        setTableKeys();
    })
    /* RECIPIENTS */
    function setOrderRecipientsForm() {
        const $orderRecipientsContainer = $('.order-recipients-container'),
            $otherRecipientsRadio = $orderRecipientsContainer.find('input[name="is_other_recipients"]');

        setAddRecipientButton($orderRecipientsContainer);
        setRemoveRecipientButton($orderRecipientsContainer);
        $otherRecipientsRadio.each(function () {
            $(this).prop('required', true);
            $(this).next('label').andSelf().wrapAll('<div class="recipients-option-wrapper"/>');
        });
    }
    function setAddRecipientButton($recipientsContainer) {
        const $recipientsList = $recipientsContainer.find('ul.recipients-list'),
            $addRecipientsBtn = $recipientsContainer.find('.add-recipient-btn');

        $addRecipientsBtn.on('click', function () {
            const $recipients = $recipientsList.find('.single-recipient'),
                $firstRecipientItem = $($recipients.get(0));
            const $recipientClone = $firstRecipientItem.clone();

            $recipientClone.find('input').each((idx, input) => {
                const name = $(input).prop('name'),
                    id = $(input).prop('id');

                $(input).prop('name', `${name}_${$recipients.length + 1}`);
                $(input).prop('id', `${id}_${$recipients.length + 1}`);
                $(input).val('');
            });
            $recipientsList.append($recipientClone);
            setRemoveRecipientButton($recipientsContainer)
        });
    }
    function setRemoveRecipientButton($recipientsContainer) {
        const $removeRecipientsBtn = $recipientsContainer.find('.remove-recipient-btn');
        $removeRecipientsBtn.off('click');
        $removeRecipientsBtn.on('click', function () {
            const $allRecipients = $recipientsContainer.find('ul.recipients-list li')
            $allRecipients.length > 1
                ? $(this).closest('li').remove()
                : $('input#is_other_recipients_false').prop('checked', true).change();
        });
    }

    /* DELIVERY */
    function setDeliveryForm() {
        const $form = $('form[name="checkout"]'),
            $deliveryMethodSelect = $form.find('select#coderockz_woo_delivery_delivery_selection_box');
        $deliveryMethodSelect.find('option:first-child').prop('disabled', true).text('יש לבחור מהרשימה'),
            setAvailableDeliveryMethods($form);
        setFormEvents($form);
    }
    function getCityInput($form) {
        const $userBillingCityInput = $form.find('input#billing_city'),
            $userShippingCityInput = $form.find('input#shipping_city'),
            $differentAddresInput = $form.find('input#ship-to-different-address-checkbox');

        return $differentAddresInput.is(':checked')
            ? $userShippingCityInput
            : $userBillingCityInput;
    }
    function getUserShippingCity($form) {
        const cityInput = getCityInput($form);
        return cityInput.val();
    }
    function getShippingData($form) {
        const $citiesInputs = $form.find('#order_review input[name="zone-cities-list"]');
        const city = getUserShippingCity($form);
        let isShippingAvailable = false;
        let zoneId = '';
        $citiesInputs.each(function () {
            if ($(this).val().split(', ').includes(city)) {
                isShippingAvailable = true;
                zoneId = $(this).siblings('input.shipping_method').attr('id')
            }
        });
        return { isShippingAvailable, zoneId };
    }
    function setAvailableDeliveryMethods($form) {
        const $pickupRadioInput = $form.find('input#shipping_method_0_local_pickup1'),
            $methodSelect = $form.find('#coderockz_woo_delivery_setting_wrapper select'),
            $deliverySelectOption = $methodSelect.find('option[value="delivery"]'),
            $pickupSelectOption = $methodSelect.find('option[value="pickup"]');

        const shippingData = getShippingData($form);
        const isShippingAvailable = shippingData.isShippingAvailable;
        const isDeliveryOptionDisabled = $deliverySelectOption.prop('disabled');
        const disabledDeliveryHtml = '<span class="shipping-alert"> לא ניתן עבור ישוב מגוריך</span>';
        if (!isShippingAvailable) {
            if (!isDeliveryOptionDisabled) {
                $pickupSelectOption.prop('selected', true);
                $deliverySelectOption.prop('disabled', true).append(disabledDeliveryHtml);
                $pickupRadioInput.prop('checked', true).trigger('change');
                $methodSelect.val($pickupSelectOption.val()).trigger('change');
            }
        } else {
            if (isDeliveryOptionDisabled) {
                $deliverySelectOption.prop('disabled', false).find('.shipping-alert').remove();
            }
        }
    }
    function setDeliveryZone($form) {
        const shippingData = getShippingData($form);
        $(`input#${shippingData.zoneId}`).prop('checked', true).change();
    }
    function setFormEvents($form) {
        const $differentAddresInput = $form.find('input#ship-to-different-address-checkbox'),
            $deliveryMethodSelect = $form.find('select#coderockz_woo_delivery_delivery_selection_box');

        const cityInput = getCityInput($form);
        const $pickupRadioInput = $('input#shipping_method_0_local_pickup1');
        $pickupRadioInput.prop('checked', true).change();

        $deliveryMethodSelect.on('change', function () {
            if ($(this).val() == 'delivery') setDeliveryZone($form);
            else {
                let $pickupRadioInput = $('input#shipping_method_0_local_pickup1');
                $pickupRadioInput.prop('checked', true).change();
            }
        });
        cityInput.on('change', () => {
            const $deliveryMethodSelect = $form.find('select#coderockz_woo_delivery_delivery_selection_box');
            setAvailableDeliveryMethods($form);
            if ($deliveryMethodSelect.val() == 'delivery') setDeliveryZone($form);
        });
        $differentAddresInput.on('change', () => {
            const newCityInput = getCityInput($form);
            cityInput.off('change');
            setAvailableDeliveryMethods($form);
            if ($deliveryMethodSelect.val() == 'delivery') setDeliveryZone($form);
            newCityInput.on('change', () => {
                // const $deliveryMethodSelect = $form.find('select#coderockz_woo_delivery_delivery_selection_box');
                setAvailableDeliveryMethods($form);
                if ($deliveryMethodSelect.val() == 'delivery') setDeliveryZone($form);
            });
        });

    }

    /* ORDER TABLE */// Function to perform the desired actions
// Function to perform the desired actions
function performActionsOnTable() {
    // Check if there are already '.row-wrapper' elements
    if ($('table.shop_table tr.cart_item .row-wrapper').length > 0) {
        return; // If elements are already present, exit to prevent multiple executions
    }

    const $labels = $('table.shop_table tr.cart_item dt');
    $labels.each(function () {
        $(this).next().andSelf().wrapAll('<div class="row-wrapper"/>');
    });
    $('table.shop_table tr.cart_item dd.variation-vegan,table.shop_table tr.cart_item dd.variation-gluten').remove();
    $('table.shop_table tr.cart_item dt.variation-vegan,table.shop_table tr.cart_item dt.variation-gluten').each(function () {
        const currentText = $(this).text(),
            formattedText = currentText.replace(':', '');
        $(this).text(formattedText);
    });

    $('table.shop_table ul.wc-item-meta li:contains("קישור תמונה")').hide();
}

// Function to create a MutationObserver
function setTableKeys() {
    const tableElement = $('table.shop_table')[0]; // Get the DOM element

    const observer = new MutationObserver(function (mutationsList, observer) {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.target === tableElement) {
                // All AJAX requests are completed, perform the actions
                $(document).ajaxStop(function () {
                    performActionsOnTable();
                });
                break; // You can remove this if you want to continue observing
            }
        }
    });

    // Configure and start the MutationObserver
    const config = { childList: true, subtree: true };
    observer.observe(tableElement, config);
}

// Start observing changes
setTableKeys();


})(jQuery)