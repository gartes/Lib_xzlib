mobOperator=[{k:'050',o:'mtc'},{k:'099',o:'mtc'},{k:'066',o:'mtc'},{k:'095',o:'mtc'},{k:'067',o:'kyivstar'},{k:'098',o:'kyivstar'},{k:'096',o:'kyivstar'},{k:'097',o:'kyivstar'},{k:'093',o:'live'},{k:'063',o:'live'},{k:'073',o:'live'},{k:'068',o:'Beeline'},{k:'091',o:'Utel'},{k:'092',o:'peoplenet'},];

/**
 *
 * @constructor
 */
function JsXzlib() {
    let $=jQuery ;

    this.Setting = {
        objDef : {
            option: 'com_ajax',
            group: 'system',
            plugin: 'Xlib',
            TRTRTRRRRRRRRRRTR:9889889898989,
        },
    };
    this.maskPhone ={
        assettLoadMask : false ,
        Settings :{
            country:'UA',
             mask : '+38(000)000-00-00' ,
            // mask : ['8(000)000-00-00','+38(000)000-00-00'] ,
        },
        Init : ( element )=>{
            if (!$(element).parent().hasClass('wrapMaskPhone') ){
                $(element).wrap("<div class='wrapMaskPhone'></div>");
            }
            let _load = this.Ajax.Assets.load ;
            let maskPhone = this.maskPhone

            if ( !maskPhone.assettLoadMask ) {
                Promise.all([


                    _load.js('/libraries/xzlib/app/document/assets/js/plg/jquery.mask.min.js?v=1'),
                    _load.css('/libraries/xzlib/app/document/assets/css/plg/inputmask.css?v=1'),

                ]).then(function() {

                    console.log('Everything has loaded!');

                    maskPhone.assettLoadMask = true ;
                    maskPhone._startMask (element);


                }).catch(function() {
                    console.log('Oh no, epic failure!');
                });
            } else{
                maskPhone._startMask (element);
            }






           //  maskJqInitStart (element);
            console.log( element ) ;
        },
        _startMask:(element)=>{



            $(element).mask(this.maskPhone.Settings.mask ,{
                onKeyPress:this.maskPhone.onKeyPress,
                onChange: function(cep){
                    console.log('cep changed! ', cep);
                },
            });
            this.maskPhone._setOperatorIcon(element)
            //$(element).trigger('change.mask')
        },
        /**
         * Установить мобильного оператора для елемента
         * @param currentField - елемент под маской
         * @private
         */
        _setOperatorIcon: (currentField) => {
            if (this.maskPhone.Settings.country != 'UA') return;
            let v = $(currentField).val();
            if (v.length >= 7) {
                let result = v.match(/\d+/g);
                $.each(mobOperator, function (i, o) {
                    if (result[1] == +o.k) {
                        $(currentField).addClass('oprValid').parent().attr('op', o.o).addClass('on');
                    }
                });
            } else {
                $(currentField).removeClass('oprValid').parent().attr('op', '').removeClass('on')
            }
        },
        /**
         * Событие ввод цифр в поле маски
         * @param v             значение поля
         * @param event         событие
         * @param currentField  текущие поле
         * @param options       опции объекта  mask
         */
        onKeyPress : (v,event,currentField,options)=>{
            this.maskPhone._setOperatorIcon(currentField);

        },




    };
    this.Ajax={
        Assets: {
            load: (function () {
                // Function which returns a function: https://davidwalsh.name/javascript-functions
                function _load(tag) {
                    return function (url) {
                        // This promise will be used by Promise.all to determine success or failure
                        return new Promise(function (resolve, reject) {
                            var element = document.createElement(tag);
                            var parent = 'body';
                            var attr = 'src';

                            // Important success and error for the promise
                            element.onload = function () {
                                resolve(url);
                            };
                            element.onerror = function () {
                                reject(url);
                            };

                            // Need to set different attributes depending on tag type
                            switch (tag) {
                                case 'script':
                                    element.async = true;
                                    break;
                                case 'link':
                                    element.type = 'text/css';
                                    element.rel = 'stylesheet';
                                    attr = 'href';
                                    parent = 'head';
                            }

                            // Inject into document to kick off loading
                            element[attr] = url;
                            document[parent].appendChild(element);
                        });
                    };
                }
                return { css: _load('link'), js: _load('script'), img: _load('img') }
            })(),
        },
        Helper:{
            addParams : function (addData){
                if ('undefined'=== typeof addData){
                    return ;
                }
                return '&'+$.param(addData);
            },
            testData : function(obj , addData){
                let $=jQuery ;

                if (typeof obj === 'object'){
                    if ( $(obj).prop("tagName") ==='FORM' ) {
                        return obj.serialize() + this.addParams( addData ) ;
                    }else{
                        return obj
                    }

                }
            },
            send:function ( obj , addData) {
               obj = this.testData(obj , addData);



                return new Promise((resolve, reject) => {
                    jQuery.ajax({
                        type: "POST"
                        , cache: false
                        , dataType: "json"
                        , timeout: "20000"
                        , url: Xzlib.Options.SiteUrl + (Xzlib.Options.isAdmin?'administrator/':'')+'index.php?'
                            + '&format=json'
                            + Xzlib.Options.XzLibLang
                            + (Xzlib.Options.itemId ? "&Itemid=" + Xzlib.Options.itemId : '')
                            + '&XzlibRequest=1'
                        , data: obj

                    })
                        .done(function (r, textStatus) {
                            if (!r.data){
                                reject(r);
                            }
                            resolve(r);
                            /*

                            if (!datas.success && datas.data == null) {

                                if (datas.message && datas.message.length > 0) {
                                    alert(datas.message);
                                    return;
                                }

                            }
                            if (typeof (callback) === "function") {
                                callback(datas);
                            }*/
                        })
                        .fail(function () {
                            console.error("Error:Xzlib.sendRequest");
                        })
                        .always(function () {
                            Xzlib.Helper.XzRequestDATA = {};

                            // Скрыть прелоадер
                            Xzlib.Helper.stopPreload();
                            //  $(form).find('[name="view"]').val( 'cart' );
                            //  NProgress.done() ;
                        });
                });
            }
        },    
    };
    
    
}
document.addEventListener("DOMContentLoaded", function () {
    let $ = jQuery ;
    let Xzlib = new JsXzlib();
    //  Переопределение событий маски
    // Xzlib.maskPhone.onKeyPress = function(v,event,currentField,options){ alert(v) };
   Xzlib.maskPhone.Init($('#bypv_billing_address_phone_2_field'))

    // Повесить Масски на все input type="tel"
    $( Xzlib.maskPhone.Init($('[type="tel"]')))

});



"undefined" === typeof Xzlib && (Xzlib = {});


Xzlib_plgInit = function () {
    var $ = jQuery;

    // Очистить html разметку модального окна
    $.fn.XzlibModal_empty = function () {
        var konteyner = $("#fancybox_konteyner");
        konteyner.find('.fancybox_head').addClass('hide').empty();
        konteyner.find('.fancybox_content').empty();
    };
    //  Установить заголовок модального окна
    $.fn.XzlibModal_setHead = function (Head) {
        var konteyner = $("#fancybox_konteyner");
        konteyner.find('h2.fancybox_head_text').html(Head)
        konteyner.find('.fancybox_head').removeClass('hide');
        return this;
    };
    // Установить контент модального окна
    $.fn.XzlibModal_setContent = function (Content) {
        var konteyner = $("#fancybox_konteyner");
        var contentBLK = konteyner.find('.fancybox_content');
        $(contentBLK).empty();
        if ('object' === typeof Content) {
            $.each(Content, function (i, itemArr) {
                if ('object' === typeof itemArr) {
                    $.each(itemArr, function (ii, item) {
                        $(contentBLK).append($('<div />', {
                            class: i,
                            html: item,
                        }));
                    })//END EACH    
                }//END IF
            })//END EACH
        } else if ('string' === typeof Content) {
            $(contentBLK).html(Content)
        }//END IF
        konteyner.find('.fancybox_content').removeClass('hide');
        return this;
    };
    $.fn.XzlibSetTrigger = function () {
        $(this).find('[data-ontrigger]').on('click', function () {
            var onTrigger = $(this).data('ontrigger');
            var offTrigger = $(this).data('offtrigger');
            var ctrl = $(this).closest('.ctrl');
            var hold_ctrl = $(this).data('hold_ctrl');

            console.log(typeof  hold_ctrl !== 'undefined')


            if (hold_ctrl == 1) {
                ctrl.addClass('hold')
            } else {
                ctrl.removeClass('hold');
            }


            $('[data-blk_id="' + offTrigger + '"]').hide();
            $('[data-blk_id="' + onTrigger + '"]').show();

        });
        return this;
    };
};


Xzlib.modulHelper = {
    CONS: {
        modulBlock_Load: 'modulBlock__Load_'
    },

    /*
    * Дозагрузка модулей
    *
    *
    */
    reloadModulBlock: function () {
        var $ = jQuery;
        var blk = $('[data-xzlib="reloadBlock"]');
        var arrEl = [];
        var loadPerf = this.CONS.modulBlock_Load;

        if (blk.length == 0) return;


        $.each(blk, function (i, el) {
            var obj = {
                id: loadPerf + i,
                view: $(el).attr('data-view'),
            };
            $(el).attr('data-load_id', obj.id);
            arrEl.push(obj);
        });

        Xzlib.Helper.XzRequestDATA = {
            elements: arrEl,
            XzlibRequest: 1,
            option: 'com_ajax',
            group: 'system',
            plugin: 'Xlib',
            format: 'json',

            component: 'com_moduls',
            view: 'copon_user',
            task: 'modulBlock_Load',
        };

        Xzlib.Helper.sendRequest(Xzlib.modulHelper.reloadModulBlockCallback);

    },
    reloadModulBlockCallback: function (d) {
        var $ = jQuery;
        var loadPerf = Xzlib.modulHelper.CONS.modulBlock_Load;
        if (d.success) {
            $.each(d.data.elements, function (i, elem) {
                $('[data-load_id=' + loadPerf + i + ']')
                    .removeAttr('data-load_id')
                    .html(elem.html)
                    .XzlibSetTrigger();
            });

        }
    },
};
/**
 * Загрузчик ресурсов css, js, img
 * // Usage:  Load different file types with one callback
 *  Promise.all([
 *      Xzlib_load.js('lib/highlighter.js'),
 *      Xzlib_load.js('lib/main.js'),
 *      Xzlib_load.css('lib/highlighter.css'),
 *      Xzlib_load.img('images/logo.png')
 *  ]).then(function() {
 *     console.log('Everything has loaded!');
 *  }).catch(function() {
 *       console.log('Oh no, epic failure!');
 *  });
 *
 * @type {{css, js, img}}
 */
Xzlib_load = (function () {
    // Function which returns a function: https://davidwalsh.name/javascript-functions
    function _load(tag) {
        return function (url) {
            // This promise will be used by Promise.all to determine success or failure
            return new Promise(function (resolve, reject) {
                var element = document.createElement(tag);
                var parent = 'body';
                var attr = 'src';

                // Important success and error for the promise
                element.onload = function () {
                    resolve(url);
                };
                element.onerror = function () {
                    reject(url);
                };

                // Need to set different attributes depending on tag type
                switch (tag) {
                    case 'script':
                        element.async = true;
                        break;
                    case 'link':
                        element.type = 'text/css';
                        element.rel = 'stylesheet';
                        attr = 'href';
                        parent = 'head';
                }

                // Inject into document to kick off loading
                element[attr] = url;
                document[parent].appendChild(element);
            });
        };
    }

    return {
        css: _load('link'),
        js: _load('script'),
        img: _load('img')
    }
})();
"undefined" === typeof Xzlib.Helper && (Xzlib.Helper = {});
/**
 * Объект оброботки Ajax
 * @type {{objSend: (function(*, *=): *), Helper: {send: (function(*=): Promise<any>)}}}
 */
Xzlib.Ajax={
    send:(obj, opt)=>{
        if (jQuery( obj ).prop("tagName") === 'FORM' ){

        }else{

            return Xzlib.Ajax._objSend(obj, opt);
        }
    },
    
    /**
     * подготовка OBJ для ajax запроса 
     * @param obj - передоваемый объект
     * @param opt - настройки метода
     * @return {*} Promise
     */
    _objSend:(obj, opt)=>{
        var $ = jQuery;
        var XzLibOpt = Joomla.getOptions('XzLib');
        var objDef = {
            option: 'com_ajax',
            group: 'system',
            plugin: 'Xlib',
        };

        var optDef = {
            action: 'objEfect',
            blockPreloader: $('body'),
            style: 'roll',
        };

        // токен сессии
        objDef[XzLibOpt.T] = 1;

        var _obj = $.extend(true, objDef, obj);

        var PreloadSetting = $.extend(true, optDef, opt);

        Xzlib.Helper.startPreload( PreloadSetting );
        return Xzlib.Ajax.Helper.send( _obj );
    },
    Helper: {
        send: (obj) => {

            return new Promise((resolve, reject) => {
                jQuery.ajax({
                    type: "POST"
                    , cache: false
                    , dataType: "json"
                    , timeout: "20000"
                    , url: Xzlib.Options.SiteUrl + "index.php?"
                        + "&format=json"
                        + Xzlib.Options.XzLibLang
                        + (Xzlib.Options.itemId ? "&Itemid=" + Xzlib.Options.itemId : '')
                        + '&XzlibRequest=1'
                    , data: obj
                })
                    .done(function (datas, textStatus) {
                        resolve(datas);
                        /*

                        if (!datas.success && datas.data == null) {

                            if (datas.message && datas.message.length > 0) {
                                alert(datas.message);
                                return;
                            }

                        }
                        if (typeof (callback) === "function") {
                            callback(datas);
                        }*/
                    })
                    .fail(function () {
                        console.error("Error:Xzlib.sendRequest");
                    })
                    .always(function () {
                        Xzlib.Helper.XzRequestDATA = {};

                        // Скрыть прелоадер
                        Xzlib.Helper.stopPreload();
                        //  $(form).find('[name="view"]').val( 'cart' );
                        //  NProgress.done() ;
                    });
            });

        },
    },
};



Xzlib.Helper = {

    XzRequestDATA: {},


    init: function () {

        if (typeof Joomla.getOptions === 'function') {
            Xzlib.Options = Joomla.getOptions('XzLib');
        }else {
            Xzlib.Options = Joomla.optionsStorage.XzLib;
        }


        // console.log(Xzlib.Options);
        // Загрузить общии стили Xzlib
        Xzlib_load.css('/libraries/xzlib/app/document/assets/css/xzlib.css');

        // инит плагина
        Xzlib_plgInit();

        // Загрузить HTML разметку модального окна Xzlib
        // Xzlib.Helper.Load_fancybox_konteyner();

        // Дозагрузка скрытых модулей.
        Xzlib.modulHelper.reloadModulBlock();
    }
    ,

    /**
     * Отправить запрос на сервер
     *
     * @version 3.0
     * @param dataRequest
     * @param callback
     * @return {Promise<any>}
     */
    send:(dataRequest , callback )=>{
        var $=jQuery ;

        var d = $.extend(true ,Xzlib.Helper.XzRequestDATA , dataRequest ) ;
        console.log( d ) ;

        return new Promise( (resolve, reject) => {
            $.ajax({
                type: "POST"
                , cache: false
                , dataType: "json"
                , timeout: "20000"
                , url: Xzlib.Options.SiteUrl + "index.php?"
                    + "&format=json"
                    + Xzlib.Options.XzLibLang
                    + (Xzlib.Options.itemId ? "&Itemid=" + Xzlib.Options.itemId : '')
                    + '&XzlibRequest=1'
                , data: Xzlib.Helper.XzRequestDATA
            }).done(function (datas, textStatus) {
                if (!datas.success && datas.data == null) {

                    if (datas.message && datas.message.length > 0) {
                        alert(datas.message);
                        return;
                    }

                }
                if (typeof (callback) === "function") {
                    callback(datas);
                }
            }).fail(function () {
                console.error("Error:Xzlib.sendRequest");
            }).always(function () {
                Xzlib.Helper.XzRequestDATA = {};

                // Скрыть прелоадер
                Xzlib.Helper.stopPreload();
                //  $(form).find('[name="view"]').val( 'cart' );
                //  NProgress.done() ;
            });
        });
    },
    /**
     * Отправить запрос
     *
     */
    sendRequest: function (callback) {

        jQuery.ajax({
            type: "POST"
            , cache: false
            , dataType: "json"
            , timeout: "20000"
            , url: Xzlib.Options.SiteUrl + "index.php?"
                + "&format=json"
                + Xzlib.Options.XzLibLang
                + (Xzlib.Options.itemId ? "&Itemid=" + Xzlib.Options.itemId : '')
                + '&XzlibRequest=1'
            , data: Xzlib.Helper.XzRequestDATA
        }).done(function (datas, textStatus) {
            if (!datas.success && datas.data == null) {

                if (datas.message && datas.message.length > 0) {
                    alert(datas.message);
                    return;
                }

            }
            if (typeof (callback) === "function") {
                callback(datas);
            }
        }).fail(function () {
            console.error("Error:Xzlib.sendRequest");
        }).always(function () {
            Xzlib.Helper.XzRequestDATA = {};

            // Скрыть прелоадер
            Xzlib.Helper.stopPreload();
            //  $(form).find('[name="view"]').val( 'cart' );
            //  NProgress.done() ;
        });

    },


    /**
     * Ajax отпрввка объекта
     *
     * @param obj Данный для отправки
     * @param callback обработчик ответа
     * @param opt - настройки функции
     */
    objEfect: function (obj, callback, opt) {
        let $ = jQuery;
        let XzLibOpt ;

        if (typeof Joomla.getOptions === "function" ){
            XzLibOpt = Joomla.getOptions('XzLib');
        } else{
            XzLibOpt = Joomla.optionsStorage.XzLib;
        }

        var objDef = {
            option: 'com_ajax',
            group: 'system',
            plugin: 'Xlib',
        };

        var optDef = {
            action: 'objEfect',
            blockPreloader: $('body'),
            style: 'roll',
        };

        // токен сессии
        objDef[XzLibOpt.T] = 1;

        var obj = $.extend(true, objDef, obj);

        var o = $.extend(true, optDef, opt);

        Xzlib.Helper.startPreload(o);
        Xzlib.Helper.XzRequestDATA = obj;
        Xzlib.Helper.sendRequest(callback);
    }
    ,


    //
    /**
     * Подготовка данных ФОРМЫ перед отправкой
     *
     * Params :
     *  form : $(form)
     *  callback : function
     *  opt =   {
     *      action : 'g-recapha Action',      // Название события для статистики рекапча
     *      blockPreloader: $('.comentForm'), // Блок над которым запустить прелоадер
     *      style : 'roll block' ,            // css Класс стиля прелоадера
     * }
     *
     */
    formEfect: function (form, callback, opt) {

        var $ = jQuery;
        var optDef = {
            action: $(form).attr('name'),
            blockPreloader: $(form),
            style: 'roll',

        };

        var opt = $.extend(optDef, opt);

        console.log(opt)

        Xzlib.Helper.startPreload(opt);


        var gcaptcha = $(form).find('[name="g-recaptcha-response"]');
        if (!gcaptcha[0]) {
            $(form).append('<input type="hidden" name="g-recaptcha-response">')
        }

        grecaptcha.execute(Xzlib.form.sitekey, {action: opt.action}).then(function (t) {
            $(form).find('[name="g-recaptcha-response"]').val(t);
            Xzlib.Helper.XzRequestDATA = form.serialize();
            Xzlib.Helper.sendRequest(callback);
        });

    },

    // запуск прелоадера
    startPreload: function (opt) {
        var $ = jQuery;

        console.log(opt)

        var blk = $(opt.blockPreloader);

        $(blk).addClass('XzlibPreloadParBlk ' + opt.style).append($('<div />', {
            id: 'XzlibPreload',
            class: opt.style,
            html: '<div id="loader"></div>',
        }));
        $('div#XzlibPreload').show(100, () => {
            $('#XzlibPreload').css({opacity: 1})
        })
    },
    // Стоп Прелоадер
    stopPreload: function (opt) {
        var $ = jQuery;

        var optDef = {
            blockPreloader: $('.XzlibPreloadParBlk'),
        };

        var opt = $.extend(optDef, opt);

        var blk = $(opt.blockPreloader);
        $(blk).removeClass('XzlibPreloadParBlk');
        $(blk).find('#XzlibPreload').remove();
    },


    /**
     *  Загрузить HTML разметку модального окна Xzlib
     *
     */
    /*Load_fancybox_konteyner: function () {
        var $ = jQuery;

        if ($('#fancybox_konteyner')[0]) return;
        $('body').append(
            $('<div />', {id: 'fancybox_konteyner'}).load(Xzlib.Options.SiteUrl + "/libraries/xzlib/app/document/elements/fancybox/fancybox_body.html")
        );
    },*/

    /**
     * Открыть модальное окно
     *
     *
     */
    openFancyboxKonteyner: function () {

        console.info('Method "Xzlib.Helper.openFancyboxKonteyner" deprecated');

        var $ = jQuery,
            t = $("#fancybox_konteyner").html();

        $.fancybox({
            'type': "html",
            'content': t,
            'onClosed': function () {
                $('div').XzlibModal_empty();
            },
        })
    },




    objSerialize: function (obj) {
        var str = [];
        for (var p in obj)
            if (obj.hasOwnProperty(p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            }
        return str.join("&");
    },


};







/*


(function($)
{
    Joomla.getOptions = function( key, def )
    {
        var elements = document.querySelectorAll(".joomla-script-options.new"),options,option=0;
        for (var i=0,l=elements.length;i<l;i++)
        {
            option = JSON.parse(elements[i].text || elements[i].textContent);
            if(option)
            {
                options = JSON.parse(elements[i].text || elements[i].textContent);
            }
        }
        return options[key] !== undefined ? options[key] : def;
    };
    Joomla.extend = function (destination, source)
    {
        for (var p in source)
        {
            if (source.hasOwnProperty(p))
            {
                destination[p] = source[p];
            }
        }
        return destination;
    };
})(jQuery, Joomla, window, document);

*/










////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Перенести в файл модуля корзины


"undefined" === typeof Virtuemart && (Virtuemart = {});
/**
 * Триггер обновления модуля корзины protect-sc.ru
 *
 * @param el
 * @param options
 */
/*
Virtuemart.customUpdateVirtueMartCartModule = function (el, options) {
    var $=jQuery;
    if (!$('.topBlockMenuNew')[0]) {
        console.log('STOP customUpdateVirtueMartCartModule - modul Not');
        return ;
    }

    Xzlib.Helper.XzRequestDATA = {
        'XzlibRequest': 1,
        'option': 'com_ajax',
        'group': 'system',
        'plugin': 'Xlib',
        'component': 'com_virtuemart',
        'view': 'cartModule',
        'task': 'updateCart',
        'format': 'json'

    };
    Xzlib.Helper.sendRequest(callback_updateCart);
};*/

/**
 * Удаление товара из модуля корзины  protect-sc.ru
 * @param el
 */
function formCartModul_delProd(el) {
    var $ = jQuery;
    var form = $(el).closest('#formCartModul');
    var product_row = $(el).closest('.product_row');

    product_row.find('input[name^="quantity"]').val(0);
    product_row.find('input[name^="delete_"]').val(1);

    // delete_
    Xzlib.Helper.XzRequestDATA = $(el).closest('#formCartModul').serialize();
    Xzlib.Helper.sendRequest(callback_updateCart);
    // console.log( el )
}

/**
 * Коллбек онобновления модуля корзины
 *  родительский блок  - topBlockMenuNew
 * @param d полученные данные
 */
function callback_updateCart(d) {
    var $ = jQuery;
    $.each(d.data, function (cls, h) {
        $('.' + cls).html(h);

    });
}

/**
 * регистрация новых слушателей для обновления корзины
 */
/*document.addEventListener("DOMContentLoaded", function () {
    var $ = jQuery;
    $(document).off("updateVirtueMartCartModule", "body", Virtuemart.customUpdateVirtueMartCartModule);
    $(document).on("updateVirtueMartCartModule", "body", Virtuemart.customUpdateVirtueMartCartModule);
});*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////









document.addEventListener("DOMContentLoaded", function () {
    Xzlib.Helper.init();

    (function (d, e) {
        var T = '[data-role="autosized-textarea"]',
            lt = e(d);

        lt.on("keydown", T, function () {
            var t = e(this)
                , a = t.val().split("\n")
                , o = a.length + 1
                , i = t.attr("data-min-rows");
            o < i && (o = i),
                t.attr("rows", o)
        });

    })(document, jQuery)

});





"undefined" == typeof Xzlib.modul && (Xzlib.modul = {});
Xzlib.modul = {};













































