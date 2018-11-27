"undefined"===typeof Xzlib&&(Xzlib={});
"undefined"===typeof Xzlib.Stars&&(Xzlib.Stars={});




Xzlib.Stars.init = function(){
    var $ = jQuery ;
    // loadCSS('/libraries/xzlib/app/document/assets/css/stars.css');
    $('.detail-add-review-stars-i').on('hover' , Xzlib.Stars.hoverOn )
        .on('mouseout' , Xzlib.Stars.mouseoverOn )
        .on('click' , Xzlib.Stars.clickOn );
    $('.detail-add-review-tabs-i').on('click' , Xzlib.Stars.tabsOn )
};


/**
 * Звезды рейтинга
 *
 */
Xzlib.Stars.clickOn = function(){
    var $ = jQuery ;
    var el =$(this) ;
    var parent = el.closest('.detail-add-review-stars');
    var input = el.closest('.detail-add-review-rating').find('input');



    if(!el.hasClass('use')){
        el.prevAll().addClass('active use');
        el.addClass('active use');
        parent.addClass('selected');
        input.val(el.attr('name'));
        return ;
    }else{
        parent.find('.active').removeClass('active').removeClass('use');
        input.val('');
        parent.removeClass('selected');
        return ;
    }
};
Xzlib.Stars.mouseoverOn = function(){
    var $ = jQuery ;
    var parent = $(this).closest('.detail-add-review-stars');
    if(parent.hasClass('selected')){
        parent.find('.active').not('.use').removeClass('active');
        return ;
    }
    parent.find('.active').removeClass('active');
};
Xzlib.Stars.hoverOn = function (){
    var $ = jQuery ;
    $(this).addClass('active').prevAll().addClass('active');


};


/**
 *  Вкладки Отзыв о товаре и краткий коммент
 *
 *
 */
Xzlib.Stars.tabsOn = function(){
    var $ = jQuery ;
    var el =$(this) ;
    var blk = $('.reviews_form');

    if( el.hasClass('active') ){ return }

    el.closest('ul').find('.active').removeClass('active')
    el.addClass('active')


    blk.children().hide();
    blk.find('#'+el.attr('data-tab')).show();



};



/*
document.addEventListener("DOMContentLoaded", function(){
    Xzlib.Stars.init();
});*/
