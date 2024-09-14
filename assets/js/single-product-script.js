($ => {
    // Globals
    const ajaxUrl = siteConfig.ajaxUrl;
    const productId = +siteConfig.productId
    // 

    $(window).on('load', function () {
        // setDefaultAttributes();
        setCustomAttributes();
        setCustomDesign();
        setCustomerText();
        setAllergensList();
        setSubOptions();
        setImageUpload();
        updateProductPrice();

    });

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
        const $imageCanvas = $container.find('.image-canvas');

        $toggleAttributesBtn.on('click', function () {
            const $attribute = $(this).closest('.attribute-wrapper');
            $attributes.removeClass('active');
            $attribute.addClass('active');
            $attributesWrapper.addClass('active');
        });

        $attributes.each(function () {
            const $attribute = $(this);
            setGallerySearch($attribute);
            setAttributeOptionSelect($attribute);
        });
    }
    function setAttributeOptionSelect($attribute) {
        const $options = $attribute.find('.gallery-content .gallery-item');
        const $optionsRadioInput = $options.find('input[type="radio"]');
        const $attributesWrapper = $attribute.closest('.attributes-wrapper');
        const $canvas = $attribute.closest('.customize-design-container').find('.image-canvas');

        $optionsRadioInput.on('change', function () {
            const selectionUrl = $(this).val();

            $attributesWrapper.removeClass('active');
            $attribute.removeClass('active');
            setCanvasDisplay(selectionUrl, $canvas);
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
        $('.file-upload-wrapper').each(function () {
            const $wrapper = $(this);
            const $attributeWrapper = $wrapper.closest('.attribute-wrapper');
            const $attributesWrapper = $wrapper.closest('.attributes-wrapper');
            const $container = $wrapper.closest('.customize-design-container');
            const $fileInput = $wrapper.find('.file-input');
            const $uploadBox = $wrapper.find('.upload-box');
            const $changeImageBtn = $wrapper.find('.change-image-btn');
            const $canvas = $wrapper.closest('.customize-design-container').find('.image-canvas');

            // Trigger file input when clicking the box
            $uploadBox.on('click', function () {
                $fileInput.trigger('click');
            });

            // Handle file input change
            $fileInput.on('change', function (e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    setCanvasDisplay(file, $canvas); // Call to display image on canvas
                    $attributeWrapper.removeClass('active');
                    $attributesWrapper.removeClass('active');
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
                    setCanvasDisplay(file, $canvas);  // Call to display image on canvas
                    $attributeWrapper.removeClass('active');
                    $attributesWrapper.removeClass('active');
                }
            });

            // Handle 'Change Image' button click
            $changeImageBtn.on('click', function () {
                $fileInput.trigger('click'); // Reopen file input dialog
            });
        });
    }
    function setCanvasDisplay(fileOrUrl, $canvas) {
        const ctx = $canvas[0].getContext('2d');
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
            if($button.hasClass('left')) {
                canvasRotation -= 90;
            }
            if($button.hasClass('right')) {
                canvasRotation += 90;
            }
            canvasRotation = canvasRotation % 360;
            $canvas.css('transform', `rotate(${canvasRotation}deg)`);
            $canvas.offset({ top: currentCanvasOffsetTop });
        });
        
        // Add a new text box
        $('#add-text-box').on('click', function () {
            addTextBox($canvas);
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
        const ctx = $canvas[0].getContext('2d');
        const sampleText = 'Sample Text';
        const x = 50;
        const y = 50;
    
        ctx.font = '16px Arial';
        ctx.fillStyle = '#000000';
        ctx.fillText(sampleText, x, y);
    }
})(jQuery);