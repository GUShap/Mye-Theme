($ => {
    // Globals
    let selectedVariation = null;
    let itemsData = {};

    // // 
    $(window).on('load', function () {
        setCustomDataToggle();
        setCustomDesign();
        setAllergensList();
        setImageUpload();
        setColorPicker();
        setStepsButtons();
        setPrettyProductGallery();
        setCustomAddToCart();
        setPriceUpdate();
    });

    function setCustomDataToggle() {
        const $customDataContainer = $('.custom-data-container');
        const $toggleBtn = $('.custom-data-toggle-button');

        $toggleBtn.on('click', function () {
            $customDataContainer.toggleClass('active');
        });
    }
    function setAllergensList() {
        const $allergensContainer = $('details.allergies-container'),
            $allergensOptions = $allergensContainer.find('.option-wrapper:not(:has(input#no-allergens)) input'),
            $noAllergensOption = $allergensContainer.find('.option-wrapper:has(input#no-allergens) input'),
            $allAllergensCheckboxes = $allergensContainer.find('.option-wrapper input'),
            $listSearchInput = $allergensContainer.find('input#allergen-search');

        setAllergensSearch($allergensOptions.siblings('label'), $listSearchInput);
        setRequiredCheckbox();
        $allergensOptions.on('change', function () {
            const $checkedAllergens = $allergensOptions.filter(':checked');
            const isAnyAllergenChecked = $checkedAllergens.length ? true : false;
            isAnyAllergenChecked
                ? $noAllergensOption.prop({ 'disabled': true, 'checked': false })
                : $noAllergensOption.prop('disabled', false);

        });

    }
    function isAllergensListChecked() {
        const $allAllergensCheckboxes = $('details.allergies-container .option-wrapper input');
        const $checkedAllergens = $allAllergensCheckboxes.filter(':checked');

        return $checkedAllergens.length ? true : false;
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
    /**********/
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
        const $options = $attribute.find('.gallery-content .gallery-item, .gallery-content .text-item');
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
                'label': this.dataset.label,
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
                    'label': el.dataset.label,
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
    /**********/
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
                    'label': 'Custom Image',
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
                        'label': 'Custom Image',
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
        // const ctx = $canvas[0].getContext('2d', { willReadFrequently: true });
        let canvasRotation = 0;

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
            $canvas.find('img').prop('src', img.src);
            // resizeCanvas($canvas, $canvas.find('img').width(), $canvas.find('img').height());
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
            // console.log($canvas.offset());
            // $canvas.offset({ top: currentCanvasOffsetTop });
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
        $('#add-text-box').off('click');
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
    // Add text box to the canvas (placeholder function)
    function addTextBox($canvas) {
        const $canvasContainer = $canvas.parent(); // Assuming the canvas has a parent container

        // Create a new text box element
        const $textBox = $('<div contenteditable="true" cancelable="false" class="text-box">טקסט אישי...</div>');

        // Append the text box to the canvas container
        $canvasContainer.append($textBox);
        $textBox.selectText();
        $textBox.on('input', function () {
            const currentText = $(this).text();
            const newHtml = currentText.split('').map(char => `<span>${char}</span>`).join('');

            // $(this).html(newHtml);
        });
        let touchtime = 0;
        $textBox.on("click", function () {
            if (touchtime == 0) {
                // set first clicks
                touchtime = new Date().getTime();
            } else {
                // compare first click to this click and see if they occurred within double click threshold
                if (((new Date().getTime()) - touchtime) < 400) {
                    // double click occurred
                    $(this).selectText();
                    touchtime = 0;
                } else {
                    // not a double click so set as a new first click
                    touchtime = new Date().getTime();
                }
            }
        });
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
        const $canvasWrapper = $attribute.find('.canvas-container');
        const $canvasElement = $canvasWrapper.find('.image-canvas img');
        const $controlsWrapper = $canvasWrapper.find('.controls-wrapper');
        const $saveImageButton = $('#next-step');

        $saveImageButton.on('click', async function () {
            const isCanvasStep = $canvasWrapper.hasClass('active');
            // const optionID = $attribute.find('.image-preview img').data('option');
            if (!isCanvasStep) return;
            // if (!$canvasWrapper.find('.text-box').length) return;
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
                'option_id': 'custom_image',
                'label': 'Custom Image',
                'url': imageDataURL
            };
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
    $.fn.selectText = function () {
        let range, selection;
        return this.each(function () {
            if (document.body.createTextRange) {
                range = document.body.createTextRange();
                range.moveToElementText(this);
                range.select();
            } else if (window.getSelection) {
                selection = window.getSelection();
                range = document.createRange();
                range.selectNodeContents(this);
                selection.removeAllRanges();
                selection.addRange(range);
            }
        });
    };
    /***************/
    function setSingleImagePreview($container, imageData, isLoader = false) {
        const $imagePreview = $container.find('.image-preview .preview-wrapper');
        if (isLoader) {
            $imagePreview.html('<div class="loader"></div>');
            return;
        } else {
            $imagePreview.html(`<img src="${imageData.url}" data-attribute="${imageData.attribute_id}" data-label="${imageData.label}" data-option="${imageData.option_id}" alt="Canvas Preview" />`);
        }
    }

    function setMultipleImagePreview($container, imagesData) {
        const $imagePreview = $container.find('.image-preview .preview-wrapper');
        let imagesHTML = '';
        imagesData.forEach(imageData => {
            imagesHTML += `<img src="${imageData.url}" data-attribute="${imageData.attribute_id}"  data-label="${imageData.label}" data-option="${imageData.option_id}" alt="Canvas Preview" />`;
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
            addAttributesToItemData($container);
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

    function addAttributesToItemData($container) {
        const $attributes = $container.find('.attribute-wrapper');
        itemsData = {};
        $attributes.each(function () {
            const $attribute = $(this);
            const $previewImages = $attribute.find('.image-preview .preview-wrapper img');

            $previewImages.each(function () {
                const $image = $(this);
                const attrID = $image.data('attribute');
                const optionID = $image.data('option');
                const url = $image.attr('src');
                const label = $image.data('label');

                if (!itemsData[attrID]) itemsData[attrID] = {};
                itemsData[attrID][optionID] = { image_src: url, label };
            });
        });

        $.ajax(
            {
                url: siteConfig.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'add_attributes_to_item',
                    item_data: itemsData
                },
                success: function (response) {
                    updateProductAfterDesign(response.data, itemsData, $container);
                }
            }
        );

    }

    function updateProductAfterDesign(itemDataRes, itemData, $container) {
        const addedPrice = itemDataRes.added_price;
        const resGalleryHTML = itemDataRes.gallery_html;
        const gallerySelector = itemDataRes.gallery_selector;
        const formatteditemsData = [...Object.entries(itemData)].map(([key, value]) => {
            return { [key]: Object.keys(value) };
        });


        const $addedPriceInput = $('input#added-price');
        const $customAttributesInput = $('input#custom-attributes');

        $container.closest('.custom-data-container').removeClass('active');
        if (!formatteditemsData.length) return;
        $addedPriceInput.val(addedPrice);
        $customAttributesInput.val(JSON.stringify(formatteditemsData));
        $(gallerySelector).html(resGalleryHTML);

        updateProductPrice(selectedVariation)
        setPrettyProductGallery();
    }

    function setPrettyProductGallery() {
        const interval = setInterval(() => {
            if ($('.pretty-product-gallery').length) {
                clearInterval(interval);
                $('.slider-for').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    lazyLoad: 'progressive',
                    asNavFor: '.slider-nav',
                    adaptiveHeight: true,
                    variableWidth: false,
                    rtl: true,
                    responsive: [
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1,
                                asNavFor: null,
                            }
                        }
                    ],
                });

                if ($('.slider-nav').length) {
                    $('.slider-nav').slick({
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        asNavFor: '.slider-for',
                        lazyLoad: 'progressive',
                        centerMode: true,
                        focusOnSelect: true,
                        variableWidth: false,
                        rtl: true,
                        centerPadding: '0',
                        prevArrow: '<i class="dashicons dashicons-arrow-left-alt2 thumbnail-arrow thumbnail-prev-arrow"></i>',
                        nextArrow: '<i class="dashicons dashicons-arrow-right-alt2 thumbnail-arrow thumbnail-next-arrow "></i>',
                    });
                }

                $('.slider-for').slickLightbox({
                    src: 'src',
                    itemSelector: '.product-image img',
                    imageMaxHeight: 0.95,
                    arrows: false,
                });
            }
        }, 10);
    }
    /********************/

    function setPriceUpdate() {
        $('body').on('found_variation', function (e, variation) {
            selectedVariation = variation;
            updateProductPrice(variation);
        });
    }

    function updateProductPrice(variation) {
        if (!variation) return;
        const currentPrice = +variation.display_price;
        const addedPrice = +$('input#added-price').val();
        let priceHTML = variation.price_html;
        const $variationPriceWrapper = $('.woocommerce-variation-price');

        if (addedPrice) {
            const newPrice = currentPrice + addedPrice;
            priceHTML = priceHTML.replace(currentPrice, newPrice);
        }

        $variationPriceWrapper.html(priceHTML);
    }

    /********************/

    function setCustomAddToCart() {

        // wc_add_to_cart_params is required to continue, ensure the object exists
        if (typeof wc_add_to_cart_params === 'undefined')
            return false;

        // Ajax add to cart
        $(document).on('click', '.variations_form .single_add_to_cart_button', function (e) {

            e.preventDefault();

            const $variation_form = $(this).closest('.variations_form');
            const isAllergensChecked = isAllergensListChecked();
            //attributes = [];
            $('.ajaxerrors').remove();
            let item = {},
                check = true;

            let variations = $variation_form.find('select[name^=attribute]');

            /* Updated code to work with radio button - mantish - WC Variations Radio Buttons - 8manos */
            if (!variations.length) {
                variations = $variation_form.find('[name^=attribute]:checked');
            }

            /* Backup Code for getting input variable */
            if (!variations.length) {
                variations = $variation_form.find('input[name^=attribute]');
            }

            variations.each(function () {

                let $this = $(this),
                    attributeName = $this.attr('name'),
                    attributevalue = $this.val(),
                    index,
                    attributeTaxName;

                $this.removeClass('error');

                if (attributevalue.length === 0) {
                    index = attributeName.lastIndexOf('_');
                    attributeTaxName = $this.data('label');

                    $this
                        .addClass('required error')
                        .before(`<div class="ajaxerrors"><p>יש לבחור ${attributeTaxName}</p></div>`)

                    check = false;
                } else {
                    item[attributeName] = attributevalue;
                }

            });

            if (!isAllergensChecked) {
                check = false;
                $('.allergens-container')
                    .addClass('required error')
                    .prepend(`<div class="ajaxerrors"><p>יש לבחור אלרגנים בכדי להוסיף לסל</p></div>`);

            }
            if (!check) {
                return false;
            }

            var $thisbutton = $(this);

            if ($thisbutton.is('.variations_form .single_add_to_cart_button')) {

                $thisbutton.removeClass('added');
                $thisbutton.addClass('loading');

                const atcData = {
                    action: 'woocommerce_add_to_cart_variable_rc',
                    items_data: itemsData
                };

                $variation_form.serializeArray().map(function (attr) {
                    if (attr.name !== 'add-to-cart') {
                        if (attr.name.endsWith('[]')) {
                            let name = attr.name.substring(0, attr.name.length - 2);
                            if (!(name in atcData)) {
                                atcData[name] = [];
                            }
                            atcData[name].push(attr.value);
                        } else {
                            atcData[attr.name] = attr.value;
                        }
                    }
                });

                // Trigger event
                $('body').trigger('adding_to_cart', [$thisbutton, atcData]);

                // Ajax action
                $.post(wc_add_to_cart_params.ajax_url, atcData, function (response) {

                    if (!response) {
                        return;
                    }

                    if (response.error && response.product_url) {
                        window.location = response.product_url;
                        return;
                    }

                    // Redirect to cart option
                    if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
                        window.location = wc_add_to_cart_params.cart_url;
                        return;
                    }

                    // Trigger event so themes can refresh other areas.
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);

                });

                return false;

            } else {

                return true;
            }

        });
    }
})(jQuery);