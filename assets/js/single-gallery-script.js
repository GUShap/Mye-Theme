($=>{
    $(window).on('load',()=>{
        setGalleryImageAnimation();
    });

    function setGalleryImageAnimation(){
       const $allThemes = $('.single-theme');

       $allThemes.each(function(){
        const $allImages = $(this).find('.item-gallery img');
        if($allImages.length < 2) return;

        setInterval(()=>{
            $(this).find('.item-gallery img').each(function(){
                const currentOrder = +$(this).css('order');
                currentOrder ==1
                ?$(this).css('order', $(this).siblings().length+1)
                :$(this).css('order',currentOrder-1);
            })
        },3500);
       });
    }
})(jQuery);