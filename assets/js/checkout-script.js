($ => {
    const pickupDatesData = checkout_vars.pickup_dates;
    const pickupDateLimit = +checkout_vars.pickup_dates_limit;
    const pickupItemsCount = +checkout_vars.pickup_items_count;

    $(window).on('load', () => {
        setOrderRecipientsForm();
        // setDeliveryForm();
        setTableKeys();
        setDatepicker();
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
                const name = $(input).prop('name').replace(/\d/g, function (match) {
                    return (parseInt(match) + $recipients.length);
                })

                $(input).prop('name', name);
                $(input).prop('id', name);
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
                : $('input#is_other_recipients_').prop('checked', true).change();

                $recipientsContainer.find('ul.recipients-list li').each((recipientIdx, recipient) => {
                $(recipient).find('input').each((idx, input) => {
                    const name = $(input).prop('name').replace(/\d/g, function (match) {
                        return recipientIdx;
                    });

                    $(input).prop('name', name);
                    $(input).prop('id', name);
                });
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

    function setDatepicker() {
        var disabledDays = [];

        // Calculate the next 3 business days (excluding Friday and Saturday)
        var today = new Date();
        var businessDaysCount = 0;

        while (businessDaysCount < 3) {
            today.setDate(today.getDate() + 1); // Move to the next day
            var day = today.getDay();
            if (day !== 5 && day !== 6) { // Skip Fridays (5) and Saturdays (6)
                disabledDays.push(new Date(today)); // Add to disabled days
                businessDaysCount++;
            }
        }

        $('#pickup-date').datepicker({
            dateFormat: 'yy-mm-dd', // Customize the date format
            minDate: 0, // Disable past dates
            maxDate: '+10w', // Limit to the next 3 weeks
            beforeShowDay: function (date) {
                const formattedDate = $.datepicker.formatDate('yy-mm-dd', date);
                const day = date.getDay();
                // Disable Fridays (5) and Saturdays (6)
                if (day === 5 || day === 6) {
                    return [false, ''];
                }

                // Disable the closest 3 business days
                for (var i = 0; i < disabledDays.length; i++) {
                    if (date.toDateString() === disabledDays[i].toDateString()) {
                        return [false, ''];
                    }
                }

                if (formattedDate in pickupDatesData) {
                    const dateItemsCount = +pickupDatesData[formattedDate];
                    const isDateAvailable = dateItemsCount + pickupItemsCount <= pickupDateLimit;
                    if (!isDateAvailable) {
                        return [false, ''];
                    }
                }

                return [true, ''];
            },
            onSelect: function (dateText) {
                $('#pickup-date-value').val(dateText);
                const formattedDate = $.datepicker.formatDate('dd בMM, yy', new Date(dateText));
                $(this).val(formattedDate);
            }
        });
    }

})(jQuery)
