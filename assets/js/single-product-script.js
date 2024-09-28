($ => {
    // Globals
    const ajaxUrl = siteConfig.ajaxUrl;
    const productId = +siteConfig.productId
    // 
    $(window).on('load', function () {
        setCustomDataToggle();
        // setCustomAttributes();
        setCustomDesign();
        // setCustomerText();
        setAllergensList();
        // setSubOptions();
        setImageUpload();
        // updateProductPrice();
        setColorPicker();
        setStepsButtons();
    });

    function setCustomDataToggle() { 
        const $customDataContainer = $('.custom-data-container');
        const $toggleBtn = $('.custom-data-toggle-button');

        $toggleBtn.on('click', function () {
            $customDataContainer.toggleClass('active');
        });
    }
    function setDefaultAttributes() {
        const $tabel = $('form.variations_form table.variations'),
            $allSelectEl = $tabel.find('td.value select'),
            $cells = $tabel.find('td.value'),
            $booleanCells = $cells.filter('[data-boolean="true"]');
        const $resetVariationBtn = $('a.reset_variations');
        const setBaseByGlutenVeganAttr = () => {
            const $selectVegan = $('select#pa_vegan'),
                $selectGluten = $('select#pa_gluten'),
                $selectBase = $('select#pa_base'),
                $optionToToggle = $selectBase.find('option[value="chocolate-vanilla"]'),
                $otherOptions = $selectBase.find('option:not([value="chocolate-vanilla"])');

            setAvailableVariations($selectVegan, $selectGluten, $selectBase, $optionToToggle, $otherOptions);

            $selectVegan.on('change', function () {
                setAvailableVariations($(this), $selectGluten, $selectBase, $optionToToggle, $otherOptions);
            });
            $selectGluten.on('change', function () {
                setAvailableVariations($selectVegan, $(this), $selectBase, $optionToToggle, $otherOptions);
            });
        };
        const setAvailableVariations = ($selectVegan, $selectGluten, $selectBase, $optionToToggle, $otherOptions) => {
            if ($selectVegan.val() == 'true' && $selectGluten.val() == 'true') {
                $optionToToggle.prop({
                    'hidden': false,
                    'disable': false,
                    'selected': true
                });
                $otherOptions.prop({
                    'hidden': true,
                    'disable': true,
                    'selected': false
                });
                $selectBase.val($optionToToggle.val());
            } else {
                $optionToToggle.prop({
                    'hidden': true,
                    'disable': true,
                    'selected': false
                });
                $otherOptions.prop({
                    'hidden': false,
                    'disable': false,
                });
                if ($selectBase.val() == $optionToToggle.val()) $selectBase.val('');
            }
        };
        const disableFirstOption = () => {
            $allSelectEl.find('option:first-child').prop({
                'disabled': true,
                'hidden': true
            });

        };

        disableFirstOption();
        if (productId == 28) setBaseByGlutenVeganAttr();
        $booleanCells.each(function () {
            const $select = $(this).find('select'),
                $options = $select.find('option'),
                $checkbox = $(this).find('input[type="checkbox"]');

            $checkbox.on('change', function () {
                const isChecked = $(this).is(':checked');
                const $selectedOption = isChecked
                    ? $options.filter('[value="true"]')
                    : $options.filter('[value="false"]');

                $selectedOption.prop('selected', true);
                $select.val($selectedOption.val()).change();
            });
            $select.on('change', function () {
                const isChecked = $(this).val() == 'true';

                isChecked
                    ? $checkbox.prop('checked', true)
                    : $checkbox.prop('checked', false);
            })

        });

        $allSelectEl.on('change', function () {
            // awaits a variation to creat
            setTimeout(() => {
                if ($.active) {
                    const $customAttributes = $('form.variations_form .custom-attr-container');
                    const $step1 = $customAttributes.find('.attr-field-wrapper[data-step="1"]');

                    $step1.find('input[type="checkbox"').prop('disabled', false);

                    const interval = setInterval(() => {
                        if (!$.active) {
                            clearInterval(interval);
                            const $priceEl = $('.woocommerce-variation .woocommerce-variation-price bdi'),
                                $currencySymbol = $priceEl.find('.woocommerce-Price-currencySymbol'),
                                $customAttributesWrapper = $('.custom-attr-container .attributes-wrapper');

                            const price = +$priceEl.text().replace(/[^\d.-]/g, ''),
                                addedPrice = +$customAttributesWrapper.data('added-price');

                            $priceEl.html(`${$currencySymbol.prop('outerHTML')}${(price + addedPrice).toFixed(2)}`);
                            $customAttributesWrapper.data('variation-price', price);
                            $customAttributesWrapper.attr('data-variation-price', price);
                            disableFirstOption();
                        }
                    }, 50);
                }
            }, 20);
        });
        $resetVariationBtn.on('click', () => {
            $('.inital-field > input[type="checkbox"]').prop('disabled', true);
            $('select#pa_gluten, select#pa_vegan').each(function () {
                $(this).find('option[value="false"]').prop('selected', true);
                setTimeout(() => {
                    $(this).val('false').change();
                }, 10)
            });
        });
    }
    function setCustomAttributes() {
        const $customAttributes = $('form.variations_form .custom-attr-container');
        const $step1 = $customAttributes.find('.attr-field-wrapper[data-step="1"]');
        const $step2 = $customAttributes.find('.attr-field-wrapper[data-step="2"]')
        $step1.each(function () {
            $(this).children('input[type="checkbox"]').on('change', (e) => {
                const isChecked = $(e.target).is(':checked');
                const isBoolean = $(this).siblings().length ? false : true;
                const changeOptionsBtn = $(this).find('.change-options-btn');
                const inputId = $(e.target).prop('id');
                $(this).data('checked', isChecked).attr('data-checked', isChecked);

                if (!isBoolean && isChecked) {
                    $(this).next().show().addClass('expanded');
                }

                changeOptionsBtn.click(() => {
                    $(this).next().show().addClass('expanded');
                });

                if (isChecked) {
                    changeOptionsBtn.show();
                } else {
                    if ($(this).next().length) {
                        const $checkboxOptions = $(this).next().find('.options-list-wrapper input').filter(':checked');
                        $checkboxOptions.prop('checked', false).change();
                    }
                    changeOptionsBtn.hide();
                }
                isChecked && inputId == 'theme'
                    ? $('input#cut-in-advance').prop({ 'disabled': true, 'checked': false })
                    : $('input#cut-in-advance').prop('disabled', false);
            })
        });

        $step2.each(function () {
            const attributeId = $(this).closest('.attribute-wrapper').data('attr-id');
            const $closeBtn = $(this).find('button.close-btn');
            const $approvalBtn = $(this).find('button.approval-btn');
            const $optionsInput = $(this).find(`input[name="${attributeId}-option"]`);
            const isSearchable = $(this).find('.option-filter-wrapper').length ? true : false;
            const limit = $(this).find('.options-list-wrapper').data('limit');



            $optionsInput.on('change', function () {
                const $hiddenInput = $(this).closest('.options-list-wrapper').children('input[type="hidden"]');
                const $subOptions = $(this).siblings('.sub-items-wrapper').find('input[type="radio"]');
                const inputType = $(this).prop('type');
                const checkedItemsAmount = $optionsInput.filter(':checked').length;

                if ($(this).is(':checked') && $subOptions.length) $($subOptions[0]).prop('checked', true);
                $hiddenInput.val('');
                $optionsInput.each(function () {
                    const currentVal = $hiddenInput.val();
                    if ($(this).is(':checked')) {
                        currentVal && inputType !== 'radio'
                            ? $hiddenInput.val(currentVal + ', ' + $(this).val())
                            : $hiddenInput.val($(this).attr('id'));
                    }
                });

                if (limit) checkedItemsAmount >= limit
                    ? $optionsInput.filter(':not(:checked)').prop('disabled', 'disabled')
                    : $optionsInput.prop('disabled', false);

                $approvalBtn.click(() => { $closeBtn.trigger('click') });

                if (!checkedItemsAmount) {
                    $approvalBtn.prop('disabled', true).addClass('disabled');
                } else $approvalBtn.prop('disabled', false).removeClass('disabled');
            });

            $closeBtn.click(() => {
                const checkedOptions = $optionsInput.filter(':checked');
                $(this).hide().removeClass('expanded');
                if (!checkedOptions.length && !$('img#user-selected-image').attr('src')) {
                    $(this).siblings('.inital-field').find('input[type="checkbox"]').prop('checked', false).change();
                }
            });

            if (attributeId == 'theme') setThemeImageChoice($approvalBtn, $closeBtn);

            if (isSearchable) {
                setThemeSearch();
            }
        })
    }
    function setThemeSearch() {
        const searchInput = $('input#option-search-input');
        searchInput.on('input', function (e) {
            const term = $(this).val().toLowerCase(),
                allThemes = $(this).closest('dialog').find('.option-wrapper');

            allThemes.each(function () {
                const theme = $(this),
                    themeTerms = theme.data('terms').toLowerCase();

                themeTerms.includes(term)
                    ? theme.show()
                    : theme.hide();

            });
        });
    }
    function setThemeImageChoice($approvalBtn, $closeBtn) {
        const $themeAttrContainer = $('.attribute-wrapper[data-attr-id="theme"]'),
            $allRadioinputs = $themeAttrContainer.find('input[type="radio"]'),
            $dataInput = $themeAttrContainer.find('input[name="theme-data"]');

        const $imageUpload = $('input#user-image-upload'),
            $label = $themeAttrContainer.find('label[for="user-image-upload"]'),
            $changeImageBtn = $themeAttrContainer.find('.change-options-btn'),
            $themeOptionsWrapper = $themeAttrContainer.find('.options-list-wrapper'),
            $subHeading = $themeAttrContainer.find('.subheading-wrapper, .options-instructions');

        $imageUpload.on('change', function () {
            const fileInput = this;
            const selectedImage = $('#user-selected-image');

            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];

                if (file.type.match('image.*')) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        selectedImage.attr('src', e.target.result);
                        selectedImage.show();
                    };

                    reader.readAsDataURL(file);
                    $label.hide();
                    $subHeading.hide();
                    $themeOptionsWrapper.hide();
                    $changeImageBtn.show();

                    $allRadioinputs.prop('checked', false);
                    $dataInput.val('');
                    $approvalBtn.prop('disabled', false).removeClass('disabled');

                    $approvalBtn.click(() => { $closeBtn.trigger('click') });
                    $changeImageBtn.on('click', function () {
                        $(fileInput).val('');
                        $(this).hide();
                        selectedImage.prop('src', '').hide();
                        $label.show();
                        $subHeading.show();
                        $themeOptionsWrapper.show();
                        $approvalBtn.prop('disabled', true).addClass('disabled');
                    });
                } else {
                    alert('נא לבחור תמונה בלבד');
                }
            } else {
                // No file selected
                selectedImage.hide();
            }
        });
    }
    function setCustomerText() {
        const $customerTextContainer = $('.customer-text-container'),
            $checkboxInput = $customerTextContainer.find('.step1-wrapper input#add-customer-text'),
            $radioInputs = $customerTextContainer.find('input[name="customer-text-template"]'),
            $textInputs = $customerTextContainer.find('input[type="text"]'),
            $approvalBtns = $customerTextContainer.find('button.text-approval-btn'),
            $hiddenInput = $customerTextContainer.find('input#customer_text');

        $checkboxInput.on('change', function () {
            const isChecked = $(this).is(':checked');
            if (!isChecked) $hiddenInput.val('');
            else {
                const $checkedTemplateTextInput = $customerTextContainer.find('input[name="customer-text-template"]:checked').parent().find('input[type="text"]');
                $checkedTemplateTextInput.trigger('input');
            }
        });

        $radioInputs.on('change', function () {
            const isChecked = $(this).is(':checked');
            const $siblingTextInputs = $(this).parent().find('input[type="text"]'),
                $siblingApprovalBtn = $(this).siblings('.text-approval-btn'),
                inputId = $(this).attr('id');

            if (isChecked) {
                $textInputs.prop('disabled', true);
                $approvalBtns.prop('disabled', true);
                $siblingTextInputs.prop('disabled', false);
                $($siblingTextInputs[0]).focus();
            }

            if (isChecked && inputId === 'customer-text-template-free') {
                $('input#customer-free-text').show();
            } else $('input#customer-free-text').hide();

            $siblingTextInputs.on('input', function () {
                const inputsWithValue = $siblingTextInputs.filter(function () {
                    return $(this).val().trim() !== "";
                });
                inputsWithValue.length
                    ? $siblingApprovalBtn.prop('disabled', false)
                    : $siblingApprovalBtn.prop('disabled', true);

                $(this).siblings('.input-as-text').text($(this).val());

                const fullText = $(this).closest('label').length
                    ? $.makeArray($(this).closest('label').find('span').map(function () { return $(this).text() })).join(' ')
                    : $(this).val();

                $hiddenInput.val(fullText);
            });

        });

        $approvalBtns.on('click', function () {
            const $uncheckedOptions = $radioInputs.filter(':not(:checked)'),
                $uncheckedOptionsWrapper = $uncheckedOptions.closest('.single-template-wrapper');

            const $textInput = $(this).parent().hasClass('free-text')
                ? $(this).siblings('.inner-text-input-wrapper').find('input#customer-free-text')
                : $(this).siblings('label').find('input.free-text-template-part'),
                $textEl = $(this).parent().find('.input-as-text'),
                $changeBtn = $(this).next();

            $(this).hide();
            $textInput.hide();
            $uncheckedOptionsWrapper.hide();

            $changeBtn.show().prop('disabled', false);
            $textEl.show();
            $changeBtn.on('click', function () {
                $(this).hide().prop('disabled', true);
                $textEl.hide()

                $textInput.show();
                $(this).siblings('button.text-btn').show();
                $uncheckedOptionsWrapper.show();

                $($textInput[0]).focus();
            });
        });
    }
    function setAllergensList() {
        const $allergensContainer = $('details.allergies-container'),
            $allergensOptions = $allergensContainer.find('.option-wrapper:not(:has(input#no-allergens)) input'),
            $noAllergensOption = $allergensContainer.find('.option-wrapper:has(input#no-allergens) input'),
            $allAllergensCheckboxes = $allergensContainer.find('.option-wrapper input'),
            $listSearchInput = $allergensContainer.find('input#allergen-search'),
            $allergensDataCollect = $allergensContainer.find('input#allergens-for-product');

        setAllergensSearch($allergensOptions.siblings('label'), $listSearchInput);
        setRequiredCheckbox();
        $allergensOptions.on('change', function () {
            const $checkedAllergens = $allergensOptions.filter(':checked');
            const isAnyAllergenChecked = $checkedAllergens.length ? true : false;
            let allergensValue = '';
            isAnyAllergenChecked
                ? $noAllergensOption.prop({ 'disabled': true, 'checked': false })
                : $noAllergensOption.prop('disabled', false);

            $checkedAllergens.each(function () {
                allergensValue.length
                    ? allergensValue += ', ' + $(this).val()
                    : allergensValue = $(this).val();
            });
            $allergensDataCollect.val(allergensValue);
        });

        $noAllergensOption.on('change', function () {
            const isChecked = $(this).is(':checked');
            const $checkedAllergens = $allergensOptions.filter(':checked');
            const isAnyAllergenChecked = $checkedAllergens.length ? true : false;

            if (isChecked) $allergensDataCollect.val($(this).val());
            else if (!isAnyAllergenChecked) $allergensDataCollect.val('');
        });

        $allAllergensCheckboxes.on('change', function () {
            setAllergensApproval($allergensContainer, $allAllergensCheckboxes)
        });

        $allergensContainer.find('summary').on('click', () => {
            setTimeout(() => {
                setAllergensApproval($allergensContainer, $allAllergensCheckboxes);
                const containerTop = $allergensContainer.offset().top;
                const containerHeight = $allergensContainer.height();
                const windowHeight = $(window).height();
                const scrollTo = containerTop + containerHeight / 2 - windowHeight / 2;

                // Animate the scroll to the calculated position
                $('html, body').animate({ scrollTop: scrollTo }, 'slow');
            }, 10);
        })
    }
    function setAllergensApproval($allergensContainer, $allAllergensCheckboxes) {
        const $approvalBtn = $allergensContainer.find('.approve-alergens-btn'),
            $summary = $allergensContainer.find('summary');

        const checkedInputs = $allAllergensCheckboxes.filter(':checked');

        if ($allergensContainer.prop('open') && !checkedInputs.length) {
            $approvalBtn.prop('disabled', true);
            $summary.on('click', (e) => { e.preventDefault() });
        } else {
            $approvalBtn.prop('disabled', false).on('click', () => {
                $allergensContainer.prop('open', false);
            });
            $summary.off('click');
        }
    }
    function setAllergensSearch($options, $input) {
        $input.on('input', function () {
            const term = $(this).val();
            $options.each(function () {
                $(this).text().includes(term)
                    ? $(this).parent().show()
                    : $(this).parent().hide();
            });

        });
    }
    function setRequiredCheckbox() {
        const $allAllergensCheckboxes = $('details.allergies-container .option-wrapper input');
        const $attributesSelect = $('table.variations td select');
        const $formSubmitBtn = $('form.variations_form button[type="submit"]');

        $allAllergensCheckboxes.on('change', function () {
            const $checkedAllergens = $allAllergensCheckboxes.filter(':checked');

            if ($checkedAllergens.length) {
                $allAllergensCheckboxes.prop('required', false);
                if (+$('input.variation_id').val()) $formSubmitBtn.removeClass('disabled');
            } else {
                $allAllergensCheckboxes.prop('required', true),
                    $formSubmitBtn.addClass('disabled');
            }
        });

        $attributesSelect.on('change', function () {
            const $checkedAllergens = $allAllergensCheckboxes.filter(':checked');

            const interval = setInterval(() => {
                if (!$.active) {
                    clearInterval(interval);
                    if (!$checkedAllergens.length && !$formSubmitBtn.hasClass('disabled')) $formSubmitBtn.addClass('disabled');
                }
            }, 40);
        });
    }
    function setSubOptions() {
        const $parentOptions = $('.option-wrapper.has-sub-options > input'),
            $subOptions = $parentOptions.siblings('.sub-items-wrapper').find('.sub-option-field-wrapper input');

        $parentOptions.on('change', function () {
            $parentOptions.each((idx, inputEl) => {
                const $subOptionsWrapper = $(inputEl).siblings('.sub-items-wrapper');

                if ($(inputEl).is(':checked')) {
                    $subOptionsWrapper.slideDown(300);
                } else {
                    $subOptionsWrapper.slideUp(200);
                    $subOptionsWrapper.find('input').prop('checked', false);
                }

            })
        });

        $subOptions.on('change', function () {
            $subOptions.each((idx, inputEl) => {
                $(inputEl).is(':checked')
                    ? $(inputEl).parent().addClass('selected')
                    : $(inputEl).parent().removeClass('selected');
            });
        });
    }
    function updateProductPrice() {
        const $form = $('.product .variations_form'),
            $attributesWrapper = $form.find('.attributes-wrapper'),
            $customAttributes = $form.find('.custom-attr-container .attribute-wrapper');

        $customAttributes.find('.inital-field input[type="checkbox"]').on('change', function () {
            const isChecked = $(this).is(':checked'),
                $step1 = $(this).closest('.inital-field'),
                attrPrice = +$step1.data('price'),
                variationPrice = +$form.find('.attributes-wrapper').data('variation-price'),
                isPPo = $step1.data('ppo'),
                $price = $('.woocommerce-variation .woocommerce-variation-price bdi'),
                $currencySymbol = $price.find('.woocommerce-Price-currencySymbol');
            if (!attrPrice) return;
            const oldPrice = +$price.text().replace(/[^\d.-]/g, '');
            let newPrice = 0;
            if (!isPPo) {
                newPrice += isChecked
                    ? oldPrice + attrPrice
                    : oldPrice - attrPrice;
                $price.html(`${$currencySymbol.prop('outerHTML')}${newPrice.toFixed(2)}`);
                $attributesWrapper.data('added-price', newPrice - variationPrice);
                $attributesWrapper.attr('data-added-price', newPrice - variationPrice);
            } else {
                const $step2 = $step1.siblings('.options-field'),
                    $checkboxOptions = $step2.find('.option-wrapper input[type="checkbox"]');

                $checkboxOptions.on('change', function () {
                    const checkedCount = $checkboxOptions.filter(':checked').length;
                    newPrice = oldPrice + (checkedCount * attrPrice);
                    $price.html(`${$currencySymbol.prop('outerHTML')}${newPrice.toFixed(2)}`);
                    $attributesWrapper.data('added-price', newPrice - variationPrice);
                    $attributesWrapper.attr('data-added-price', newPrice - variationPrice);
                });
            }
        });
    }
    function setCustomDesign() {
        const $container = $('.customize-design-container');
        const $attributesWrapper = $container.find('.attributes-wrapper');
        const $attributes = $attributesWrapper.find('.attribute-wrapper');
        const $toggleAttributesBtn = $attributesWrapper.find('.attr-heading button');

        $toggleAttributesBtn.on('click', function () {
            const $attribute = $(this).closest('.attribute-wrapper');
            $attributes.removeClass('active');
            $attribute.addClass('active');
            $attributesWrapper.addClass('active');
            setAttributeActiveButtons($attribute);
        });

        $attributes.each(function () {
            const $attribute = $(this);
            setGallerySearch($attribute);
            setAttributeOptionSelect($attribute);
            setImageSelection($attribute);
        });
    }
    function setAttributeOptionSelect($attribute) {
        const $options = $attribute.find('.gallery-content .gallery-item');
        const $optionsRadioInput = $options.find('input[type="radio"]');
        const $optionsCheckboxInput = $options.find('input[type="checkbox"]');
        const $imageSelectionWrapper = $attribute.find('.image-selection-wrapper');
        const $canvas = $attribute.closest('.customize-design-container').find('.image-canvas');
        const $nextStepBtn = $('#next-step');

        $optionsRadioInput.on('change', function () {
            const selectionUrl = $(this).val();
            const imageData = {
                'attribute_id': $attribute.data('id'),
                'option_id': this.id,
                'url': selectionUrl
            }
            $imageSelectionWrapper.removeClass('active');
            $imageSelectionWrapper.next().addClass('active');
            setCanvasDisplay(selectionUrl, $canvas);
            setSingleImagePreview($attribute, imageData);
            setAttributeActiveButtons($attribute);
        });

        $optionsCheckboxInput.on('change', function () {
            const limit = $attribute.data('limit');
            const $checkedOptions = $optionsCheckboxInput.filter(':checked');
            const $uncheckedOptions = $optionsCheckboxInput.filter(':not(:checked)');
            if ($checkedOptions.length) $nextStepBtn.addClass('active');

            $checkedOptions.length >= limit
                ? $uncheckedOptions.prop('disabled', true)
                : $uncheckedOptions.prop('disabled', false);
            const imagesData = Array.from($checkedOptions).map(el => {
                return {
                    'attribute_id': $attribute.data('id'),
                    'option_id': el.id,
                    'url': el.value
                }
            });

            setMultipleImagePreview($attribute, imagesData);
        });
    }
    function setGallerySearch($attribute) {
        const $searchInput = $attribute.find('input[type="search"]');
        const $galleryItems = $attribute.find('.gallery-item');

        $searchInput.on('input', function () {
            const term = $(this).val().toLowerCase();

            $galleryItems.each(function () {
                const $item = $(this);
                const terms = $item.data('terms').toLowerCase();

                terms.includes(term)
                    ? $item.show()
                    : $item.hide();
            });
        });
    }
    function setImageUpload() {
        $('.image-selection-wrapper').each(function () {
            const $wrapper = $(this);
            const $fileInput = $wrapper.find('.file-input');
            const $hiddenFileInput = $wrapper.find('.file-input-value');
            const $uploadBox = $wrapper.find('.upload-box');
            const $changeImageBtn = $wrapper.find('.change-image-btn');
            const $canvas = $wrapper.closest('.customize-design-container').find('.image-canvas');
            const $nextStepBtn = $('#next-step');
            const $prevStepBtn = $('#prev-step');
            const $attribute = $wrapper.closest('.attribute-wrapper');

            // Trigger file input when clicking the box
            $uploadBox.on('click', function () {
                $fileInput.trigger('click');
            });

            // Handle file input change
            $fileInput.on('change', function (e) {
                const file = e.target.files[0];
                const fileURL = URL.createObjectURL(file);
                const imageData = {
                    'attribute_id': $attribute.data('id'),
                    'option_id': 'custom_image',
                    'url': fileURL
                }
                if (file && file.type.startsWith('image/')) {
                    setCanvasDisplay(file, $canvas); // Call to display image on canvas
                    setSingleImagePreview($attribute, imageData);
                    $wrapper.removeClass('active');
                    $wrapper.next().addClass('active');
                    $nextStepBtn.addClass('active');
                    $prevStepBtn.addClass('active');
                    $hiddenFileInput.val(fileURL);
                }
            });

            // Drag & drop functionality
            $uploadBox.on('dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $wrapper.addClass('dragging');
            });

            $uploadBox.on('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $wrapper.removeClass('dragging');
            });

            $uploadBox.on('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $wrapper.removeClass('dragging');

                const files = e.originalEvent.dataTransfer.files;
                const file = files[0];
                $fileInput[0].files = files;

                if (file && file.type.startsWith('image/')) {
                    const fileURL = URL.createObjectURL(file);
                    const imageData = {
                        'attribute_id': $attribute.data('id'),
                        'option_id': 'custom_image',
                        'url': fileURL
                    }
                    setSingleImagePreview($attribute, imageData);
                    setCanvasDisplay(file, $canvas);  // Call to display image on canvas
                    $wrapper.removeClass('active');
                    $wrapper.next().addClass('active');
                    $nextStepBtn.addClass('active');
                    $prevStepBtn.addClass('active');
                    $hiddenFileInput.val(fileURL);
                }
            });

            // Handle 'Change Image' button click
            $changeImageBtn.on('click', function () {
                $fileInput.trigger('click'); // Reopen file input dialog
            });
        });
    }
    function setCanvasDisplay(fileOrUrl, $canvas) {
        const ctx = $canvas[0].getContext('2d', { willReadFrequently: true });
        let canvasRotation = 0;
        let textBoxes = [];

        const img = new Image();
        if (fileOrUrl instanceof File) {
            const reader = new FileReader();
            reader.onload = function (event) {
                img.src = event.target.result;
            };
            reader.readAsDataURL(fileOrUrl);
        } else {
            img.src = fileOrUrl;
        }

        img.onload = function () {
            const isPortrait = img.height > img.width;
            if (isPortrait) {
                resizeCanvas($canvas, img.height, img.width);
                drawImageRotated($canvas, img, -Math.PI / 2);
            } else {
                resizeCanvas($canvas, img.width, img.height);
                drawImage($canvas, img);
            }
            $canvas.show();
        };

        // Rotate canvas left
        $('.rotate-canvas-button').on('click', function () {
            const $button = $(this);
            const currentCanvasOffsetTop = $canvas.offset().top;
            if ($button.hasClass('left')) {
                canvasRotation -= 90;
            }
            if ($button.hasClass('right')) {
                canvasRotation += 90;
            }
            canvasRotation = canvasRotation % 360;
            $canvas.css({
                transform: `rotate(${canvasRotation}deg)`,
            });
            $canvas.offset({ top: currentCanvasOffsetTop });
            if ($canvas.hasClass('landscape')) {
                $canvas.removeClass('landscape')
                $canvas.addClass('portrait')
            } else {
                $canvas.removeClass('portrait')
                $canvas.addClass('landscape')
            }
            $button.blur();
        });

        // Add a new text box
        $('#add-text-box').on('click', function () {
            addTextBox($canvas);
            $(this).blur();
        });
    }
    // Resize the canvas and adjust its dimensions
    function resizeCanvas($canvas, width, height) {
        $canvas[0].width = width;
        $canvas[0].height = height;
    }
    // Draw the image normally on the canvas
    function drawImage($canvas, img) {
        const ctx = $canvas[0].getContext('2d');
        ctx.clearRect(0, 0, $canvas[0].width, $canvas[0].height);
        ctx.drawImage(img, 0, 0, $canvas[0].width, $canvas[0].height);
    }
    // Draw the image rotated on the canvas
    function drawImageRotated($canvas, img, rotationAngle) {
        const ctx = $canvas[0].getContext('2d');
        ctx.save();
        ctx.translate($canvas[0].width / 2, $canvas[0].height / 2);
        ctx.rotate(rotationAngle);
        ctx.drawImage(img, -img.width / 2, -img.height / 2, img.width, img.height);
        ctx.restore();
    }
    // Add text box to the canvas (placeholder function)
    function addTextBox($canvas) {
        const $canvasContainer = $canvas.parent(); // Assuming the canvas has a parent container

        // Create a new text box element
        const $textBox = $('<div contenteditable="true" class="text-box">טקסט אישי...</div>');

        // Append the text box to the canvas container
        $canvasContainer.append($textBox);
        showTextBoxControls($textBox);

        // Make the text box draggable
        $textBox.draggable({
            containment: $canvasContainer,
            start: function () {
                $canvasContainer.find('#text-box-controls .input-wrapper').removeClass('active');
            },
            stop: function () {
                $(this).focus();
                showTextBoxControls($(this));
            }
        });
        // $textBox.focus();
        // Attach event to handle text box selection
        $textBox.on('focus', function (e) {
            e.stopPropagation(); // Prevent event bubbling
            showTextBoxControls($(this)); // Pass the selected text box for control
        });
        // Deselect text box when clicking outside
        $canvasContainer.on('click', function () {
            hideTextBoxControls(); // Hide controls when clicking outside of the text box
        });

        $textBox.on('input', function () {
            const text = $(this).text();
            setTimeout(() => {
                if (!$(this).text()) $(this).remove();
            }, 2500);
        });
    }
    // Function to show controls for the selected text box
    function showTextBoxControls($textBox) {
        const $controls = $('#text-box-controls'); // Assuming a control panel exists
        const $controlButtons = $controls.find('.input-wrapper button');
        // Display the control panel
        $controls.show();

        // Update controls with the current styles of the selected text box
        $controls.find('#font-family').val($textBox.css('font-family'));
        $controls.find('#font-size').val(parseInt($textBox.css('font-size')));
        $controls.find('#font-weight').val($textBox.css('font-weight'));
        $controls.find('#text-color').val(rgbToHex($textBox.css('color')));
        $controls.find('#bg-color').val(rgbToHex($textBox.css('background-color')));

        // Attach change events to the control inputs for the specific text box
        $controls.find('#font-family').off('change').on('change', function () {
            $textBox.css('font-family', $(this).val());
            $(this).siblings('label').find('button').click();
        });
        $controls.find('#font-size').off('input').on('input', function () {
            $textBox.css('font-size', $(this).val() + 'px');
        });
        $controls.find('#font-weight').off('input').on('input', function () {
            $textBox.css('font-weight', $(this).val());
        });
        $controls.find('#text-color').off('change').on('change', function () {
            $textBox.css('color', $(this).val());
        });
        $controls.find('#bg-color').off('change').on('change', function () {
            $textBox.css('background-color', $(this).val());
        });

        $controlButtons.off('click');
        $controlButtons.on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            const $wrapper = $(this).closest('.input-wrapper');
            const isSelect = $wrapper.hasClass('type-select');
            $wrapper.siblings('.input-wrapper').removeClass('active');
            $wrapper.toggleClass('active');
            if (isSelect) {
                $wrapper.find('select')[0].size = 4;
            }
        });
    }

    // Function to hide text box controls
    function hideTextBoxControls() {
        const $controls = $('#text-box-controls');
        // $controls.hide();
    }

    // Utility function to convert RGB to HEX color
    function rgbToHex(rgb) {
        let rgbValues = rgb.match(/\d+/g);
        let hex = '#' + rgbValues.map(x => {
            let hexVal = parseInt(x).toString(16);
            return hexVal.length == 1 ? '0' + hexVal : hexVal;
        }).join('');
        return hex;
    }
    function setColorPicker() {

        const getOptions = (selector) => {
            return {
                format: 'hexa',
                value: $(selector).val(),
                hideOnPaletteClick: true,
                onInput: function () {
                    $(selector).val(this.toString()).trigger('change');
                }
            }
        }
        $('#bg-color-button, #text-color-button').each(function () {
            $button = $(this);
            const selector = $(this).data('val-selector');
            const options = getOptions(selector)
            let isFocused = false;
            const customColorInput = new JSColor(this, options);
            $button.on('click', function () {
                isFocused = !isFocused;
                isFocused ? customColorInput.show() : customColorInput.hide();
            });
            $button.on('blur', function () {
                isFocused = false;
                customColorInput.hide();
            });
        });
    }
    function setImageSelection($attribute) {
        const $canvasWrapper = $attribute.find('.canvas-wrapper');
        const $canvasElement = $canvasWrapper.find('.image-canvas');
        const $controlsWrapper = $canvasWrapper.find('.controls-wrapper');
        const $saveImageButton = $('#next-step');

        $saveImageButton.on('click', async function () {
            const isCanvasStep = $canvasWrapper.hasClass('active');
            const optionID = $attribute.find('.image-preview img').data('option');
            if (!isCanvasStep) return;
            const canvasRect = $canvasElement[0].getBoundingClientRect();
            const dimensions = {
                x: $canvasElement.offset().left - $canvasWrapper.offset().left, // Capture only within canvas borders
                y: $controlsWrapper.height(), // Capture only within canvas borders
                width: canvasRect.width,
                height: canvasRect.height,
            }
            setSingleImagePreview($attribute, '', true);
            $controlsWrapper.find('.active').removeClass('active');
            const imageDataURL = await generateCanvasImage($canvasWrapper, dimensions);
            const imageData = {
                'attribute_id': $attribute.data('id'),
                'option_id': optionID,
                'url': imageDataURL
            };
            localStorage.setItem('imageDataURL', imageDataURL);
            setSingleImagePreview($attribute, imageData);
        });
    }
    async function generateCanvasImage($element, dimensions) {
        // Use html2canvas to capture the canvas and text-box elements within the canvas borders
        return await html2canvas($element[0], dimensions).then(function (canvas) {
            // Convert the canvas to a data URL (image format)
            const imgData = canvas.toDataURL("image/png");
            return imgData;
        });
    }

    function setSingleImagePreview($container, imageData, isLoader = false) {
        const $imagePreview = $container.find('.image-preview .preview-wrapper');
        if (isLoader) {
            $imagePreview.html('<div class="loader"></div>');
            return;
        } else {
            $imagePreview.html(`<img src="${imageData.url}" data-attribute="${imageData.attribute_id}" data-option="${imageData.option_id}" alt="Canvas Preview" />`);
        }
    }

    function setMultipleImagePreview($container, imagesData) {
        const $imagePreview = $container.find('.image-preview .preview-wrapper');
        let imagesHTML = '';
        imagesData.forEach(imageDate => {
            imagesHTML += `<img src="${imageDate.url}" data-attribute="${imageDate.attribute_id}" data-option="${imageDate.option_id}" alt="Canvas Preview" />`;
        });

        $imagePreview.html(imagesHTML);
    }

    function setStepsButtons() {
        const $container = $(".customize-design-container");
        const $nextStepBtn = $container.find("#next-step");
        const $nextAttrBtn = $container.find("#next-attr");
        const $prevStepBtn = $container.find("#prev-step");
        const $prevAttrBtn = $container.find("#prev-attr");
        const $changeSelectionBtn = $container.find(".change-selection-button");
        const $finishDesignBtn = $container.find("#finish-design");
        const $backToEditBtn = $container.find("#back-to-edit");
        const $addToItemBtn = $container.find("#add-to-item");

        $container.find('.footing-wrapper button').on('click', function () {
            $(this).blur();
        });

        $nextStepBtn.on("click", function () {
            const $activeAttr = $container.find(".attribute-wrapper.active");
            const $activeStep = $activeAttr.find(".step.active");
            const $nextStep = $activeStep.next(".step");
            const isNextStepLast = $nextStep.length && !$nextStep.next('.step').length;
            $activeStep.removeClass("active");
            $nextStep.addClass("active");
            $prevStepBtn.addClass("active");
            if (isNextStepLast) {
                $nextStepBtn.removeClass("active");
            }
        });

        $prevStepBtn.on("click", function () {
            const $activeAttr = $container.find(".attribute-wrapper.active");
            const $activeStep = $activeAttr.find(".step.active");
            const $prevStep = $activeStep.prev(".step");
            const isPrevStepFirst = !$prevStep.prev('.step').length;
            $activeStep.removeClass("active");
            $prevStep.addClass("active");
            $nextStepBtn.addClass("active");
            if (isPrevStepFirst) {
                $prevStepBtn.removeClass("active");
            }
        });

        $nextAttrBtn.on("click", function () {
            const $activeAttr = $container.find(".attribute-wrapper.active");
            const $nextAttr = $activeAttr.next(".attribute-wrapper");
            if ($nextAttr.length) {
                $activeAttr.removeClass("active");
                $nextAttr.addClass("active");
                setAttributeActiveButtons($nextAttr);
            }
        });

        $prevAttrBtn.on("click", function () {
            const $activeAttr = $container.find(".attribute-wrapper.active");
            const $prevAttr = $activeAttr.prev(".attribute-wrapper");
            if ($prevAttr.length) {
                $activeAttr.removeClass("active");
                $prevAttr.addClass("active");
                setAttributeActiveButtons($prevAttr);
            }
        });

        $changeSelectionBtn.on('click', function () {
            $prevStepBtn.trigger('click');
        });

        $finishDesignBtn.on('click', function () {
            $container.find('.attributes-wrapper').removeClass('active');
            $container.find('.totals-summary').addClass('active');
            setDesignSummary($container);
        });

        $backToEditBtn.on('click', function () {
            $container.find('.totals-summary').removeClass('active');
            $container.find('.attributes-wrapper').addClass('active');
        });

        $addToItemBtn.on('click', async function () {
            const itemDataRes = await addAttributesToItemData($container);
            if (itemDataRes.status === 'success') {
            }
        });
    }

    function setAttributeActiveButtons($attribute) {
        const $nextStepBtn = $("#next-step");
        const $nextAttrBtn = $("#next-attr");
        const $prevStepBtn = $("#prev-step");
        const $prevAttrBtn = $("#prev-attr");

        const $previewWrapper = $attribute.find('.image-preview .preview-wrapper');
        const activeStepIndex = $attribute.find('.step.active').index();
        const totalSteps = $attribute.find('.step').length;


        $attribute.next().length
            ? $nextAttrBtn.addClass('active')
            : $nextAttrBtn.removeClass('active');

        $attribute.prev().length
            ? $prevAttrBtn.addClass('active')
            : $prevAttrBtn.removeClass('active');

        if (!$previewWrapper.find('img').length) {
            $nextStepBtn.removeClass('active');
            $prevStepBtn.removeClass('active');
            return;
        }

        activeStepIndex === 0
            ? $prevStepBtn.removeClass('active')
            : $prevStepBtn.addClass('active');

        activeStepIndex === totalSteps - 1
            ? $nextStepBtn.removeClass('active')
            : $nextStepBtn.addClass('active');

    }

    function setDesignSummary($container) {
        const $attributes = $container.find('.attribute-wrapper');
        const $summaryAttributeLines = $container.find('.totals-summary .attribute-line-wrapper');

        $attributes.each(function () {
            const $attribute = $(this);
            const $previewWrapper = $attribute.find('.image-preview');
            const $images = $attribute.find('.image-preview .preview-wrapper img');
            const isMultipleImages = $previewWrapper.hasClass('multiple');
            const attrID = $attribute.data('id');
            const $reffSummaryLine = $summaryAttributeLines.filter(`[data-id="${attrID}"]`);
            const $reffSummaryLineContent = $reffSummaryLine.find('.attribute-content');

            $reffSummaryLineContent.empty();
            if ($images.length) {
                $reffSummaryLineContent.append($images.clone());
                $reffSummaryLineContent.css('grid-template-columns', `repeat(${$images.length}, 1fr)`);
                isMultipleImages
                    ? $reffSummaryLineContent.addClass('multiple')
                    : $reffSummaryLineContent.removeClass('multiple');
            }
        });
    }

    async function addAttributesToItemData($container) {
        const $attributes = $container.find('.attribute-wrapper');
        const itemData = {};

        $attributes.each(function () {
            const $attribute = $(this);
            const $previewImages = $attribute.find('.image-preview .preview-wrapper img');

            $previewImages.each(function () {
                const $image = $(this);
                const attrID = $image.data('attribute');
                const optionID = $image.data('option');
                const url = $image.attr('src');

                if (!itemData[attrID]) itemData[attrID] = {};
                itemData[attrID][optionID] = url;
            });
        });

        return await $.ajax(
            {
                url: siteConfig.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'add_attributes_to_item',
                    item_data: itemData
                },
                success: function (response) {
                    return response;
                }
            }
        );

    }
})(jQuery);