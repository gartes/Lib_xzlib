"undefined"==typeof Xzlib&&(Xzlib={});
"undefined"==typeof Xzlib.tos&&(Xzlib.tos={});



Xzlib.tos.OnClickRel = function(   ){
    var $ = jQuery ,
        myOptions = Joomla.getOptions('xzlib_app_document_elements_forms_elements_tos'),
        callback  = Xzlib.tos.OnClickRelcallback ,
        form = $('#getCouponCodeForm');



    $(form).find('[name="Article"]').val(myOptions.article_id)
    $(form).find('[name="task"]').val('getArticle');
    $(form).find('[name="component"]').val('com_content');
    Xzlib.Helper.XzRequestDATA = $(form).serialize();
    Xzlib.Helper.sendRequest( callback );
};
 
Xzlib.tos.OnClickRelcallback = function(datas){
    var $ = jQuery ; 
    var htmlContent = false;
    if( !datas.success && datas.message ){
        htmlContent = datas.message
    }else{
       htmlContent = datas.data.content 
    }
    if( htmlContent ){
        $.fancybox({
            content : htmlContent ,
            onClosed: function () {},
        })    
    }
};





































