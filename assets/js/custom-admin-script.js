($ => {
    function handleListItem(i) {
      const itemText = $(i).find(".acf-rel-item").text();
      const field = $(i).closest("td.acf-field");
      const messageField = field.siblings("td.acf-field-message");
      const buttonField = field.siblings("td.acf-field-acfe-button");
      const removeItemLink = $(i).find('a[data-name="remove_item"]');
      
      field.hide();
      messageField.find(".acf-input").text(itemText);
      buttonField.find("button").prop("type", "button");
      buttonField.on("click", function () {
        removeItemLink.click();
        $(this).closest("td.acf-field").siblings(".acf-field-relationship").show();
        $(this).off("click");
      });
    }
    
    $(window).on("load", () => {
      setProductVariations();
      setGalleryThemeFields();
      setOrderDetails();
      setOrserItems();
    });

    function setProductVariations(){
      setTableObserver();
      $("li.variations_options a,.acf-tab-group a.acf-tab-button").click(function () {
        let interval = setInterval(() => {
          if (!$.active) {
            clearInterval(interval);
            setTableObserver();
          }
        }, 50);
      });
    }
    function setTableObserver(){
      let interval = setInterval(() => {
        if (!$.active) {
          clearInterval(interval);
          (function () {
            const observer = new MutationObserver((mutationsList, observer) => {
              for (const mutation of mutationsList) {
                if (mutation.type === "childList") {
                  const addedNodes = mutation.addedNodes;
                  for (const addedNode of addedNodes) {
                    if (addedNode.tagName !== "LI") return;
                    if ($(addedNode).closest(".values").length) {
                      handleListItem(addedNode);
                    }
                  }
                }
              }
            });
            $(".acf-table").each((index, element) => {
              observer.observe(element, { childList: true, subtree: true });
            });
          })();
          $(".acf-field-relationship .values ul li").each(function () {
            handleListItem(this);
          });
        }
      }, 50);
    }
    function setGalleryThemeFields(){
      const $container = $('div.acf-field[data-name="gallery_items"], div.acf-field[data-name="attribute_options"] ');
      // $mainLabel = $container.find('.acf-label'),
      $rows = $container.find('.acf-row:not(.acf-clone)');
      
      $rows.each(function(){
        
        const $itemName = $(this).find('div.acf-field[data-name="item_name"] input[type="text"], div.acf-field[data-name="label"] input[type="text"]'),
        $titleLabels = $(this).find('.acf-accordion-title label');
        $titleLabels.text($itemName.val());
      });
    }

    // ORDERS
    function setOrderDetails(){
      const $recipientsColumn = $('#order-recipients-column');

      console.log($recipientsColumn);
    }

    function setOrserItems(){
      const $orderItems = $('#woocommerce-order-items');
      if(!$orderItems.length) return;

      const $items =  $orderItems.find('tbody#order_line_items .item');
      $items.each(function(){
        // thumbnail
        const $thumbnail = $(this).find('td.thumb .wc-order-item-thumbnail img'),
            themeImageUrl = $(this).find('tr:contains("קישור תמונה") td a').text();
           if(themeImageUrl) $thumbnail.prop('src', themeImageUrl);
        // gluten
        const $glutenRow = $(this).find('tr:contains("ללא גלוטן")'),
          $glutenRowTH = $glutenRow.find('th'), 
          $glutenRowTD =$glutenRow.find('td p')
          if($glutenRowTD.text() !=='ללא גלוטן') $glutenRow.hide();
            $glutenRowTD.remove();
            $glutenRowTH.text($glutenRowTH.text().replace(':', ''));
          // vegan
          const $veganRow = $(this).find('tr:contains("טבעוני")'),
          $veganRowTH = $veganRow.find('th'), 
          $veganRowTD =$veganRow.find('td p')
          if($veganRowTD.text() !=='טבעוני') $veganRow.hide();
            $veganRowTD.remove();
            $veganRowTH.text($veganRowTH.text().replace(':', ''));
           
      });
    }
  })(jQuery);
  