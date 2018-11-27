
"undefined"===typeof Xzlib&&(Xzlib={});
"undefined"===typeof Xzlib.form&&(Xzlib.form={});

Xzlib.form.init = function(){

    loadCSS('/libraries/xzlib/app/document/form/assets/css/form.css');



};







document.addEventListener("DOMContentLoaded", function(){
    Xzlib.form.init();

});




"undefined"===typeof jcomments&&(jcomments={});
jcomments = {
    message : function (message) {
        alert( message );

    },
    //  setAntiCache: function(c,p,f){this.aca=c;this.acp=p;this.acf=f;this.onReady(this.loadComments);},
    clear : function () {
        jQuery.fancybox.close();


    },
    error : function (message) {
        console.log( message )
    },

};
jcomments.error = function(d){
    alert(d);
    console.log(d)
};
jcomments.setAntiCache = function(){};


/*function onFulfilled(){
    alert('onFulfilled')
}*/

Comment_form = {

    getFormComment : function(event){
        var $ = jQuery ;

        var obj = {

            component:'com_jcomments',
            task: 'getFormComments' ,
            opt:{
                prodId : event.data.productId ,
               // name:'Comment',

            }
        };

        var opt =   {
            action : 'getForm_Comment',
            blockPreloader:$('.comentForm'),
            style : 'roll block' ,
        };

        Xzlib.Helper.objEfect( obj , Comment_form.callback_getFormComment , opt);


        /* */
        // Xzlib.Helper.objEfect();
    },


    /**
     * загрузка файла скрипта для коммент
     * @returns {Promise<any>}
     */
    setJs : function (){

            return new Promise(function(resolve, reject) {
                // Эта функция будет вызвана автоматически

                // В ней можно делать любые асинхронные операции,
                // А когда они завершатся — нужно вызвать одно из:
                // resolve(результат) при успешном выполнении
                // reject(ошибка) при ошибке

                var s = document.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = '/libraries/xzlib/app/document/assets/js/stars.js?v=1';

                var ss = document.getElementsByTagName('script')[0];
                ss.parentNode.insertBefore(s, ss);

                s.onload = function() {
                    resolve(ss);
                };


            });

    },

    callback_getFormComment : function(r){
        var $ = jQuery ;
        $('.comentForm').append(r.data.html);
        Comment_form.setJs().then( function () {
            Xzlib.Stars.init();
            $('[aria-controls="reviews"]').off('click.loadForm')


        } );

    },


    closeModalform : function(){
        var $=jQuery;
        var form = $('#repl_comment').find('form');
        form.find('#parent_comment_id').remove();
        $('#short_comment_form_tabs').append(form);
        $('div').XzlibModal_empty();
    },

    showReply : function( id ){

        var $=jQuery ;
        var par = $('#comment-item-'+id);

        var konteyner = $("#fancybox_konteyner");
        var contentBLK =  konteyner.find('.fancybox_content');
        var d = $( $('<div />' , {id:'repl_comment'}) );
        var parent_field  = '<input id="parent_comment_id" type="hidden" name="parent" value="'+id+'">' ;
        var form = $('#short_comment_form');

        form.append(  parent_field  );

        $('[data-tab="reviews_form_tabs"]').trigger('click');


        contentBLK.empty().removeClass('hide');
        d.appendTo( contentBLK );
        contentBLK.find('#repl_comment').append(form);

        $.fancybox({
            'type':"html",
            'content': $("#fancybox_konteyner") ,
            'onClosed' : function () {
                Comment_form.closeModalform();
            },
        });
    },


    send_form : function (evt) {
        evt.preventDefault();

        console.log(evt.target);


        var $ = jQuery,
            callback = Comment_form.send_formCallback,
            form = $(evt.target);



        form.find('[name="task"]').val('comment_addComment');
        form.find('[name="component"]').val('com_virtuemart');

        var opt =   {
            action : 'send_form_Comment',
            blockPreloader:$('.comentForm'),
            style : 'roll block' ,
        };
        Xzlib.Helper.formEfect( form , callback , opt );

        /*// Xzlib.Helper.init();
        Xzlib.Helper.Load_fancybox_konteyner();
        Xzlib.Helper.XzRequestDATA = form.serialize();
        Xzlib.Helper.sendRequest(callback);*/
    },

    send_formCallback : function (r){
        var $ = jQuery ;
        $.each(r, function (i ,data ) {
            var sText = data.d ;
            var result;

            try {
                result = eval( sText );
            }catch(e){
                console.log( e ) ;
            }
        });
    }

};































