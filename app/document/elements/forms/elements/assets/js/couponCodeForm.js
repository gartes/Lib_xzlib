"undefined"===typeof CouponCodeForm && ( CouponCodeForm = {} );
/**
 * CodeForm  submit
 * 
 * 
 * 
 */ 
document.getElementById('getCouponCodeForm').addEventListener('submit', function(evt){
    evt.preventDefault();
    CouponCodeForm.getCoupon(this);
});

CouponCodeForm = {
    getCoupon: function (form) {
        var $ = jQuery,
            callback = CouponCodeForm.CouponCallback,
            form = $('#getCouponCodeForm');

        form.find('[name="task"]').val('add_coupon_code_nUser');
        form.find('[name="component"]').val('com_virtuemart');



        // Xzlib.Helper.init();
        // Xzlib.Helper.Load_fancybox_konteyner();

        Xzlib.Helper.formEfect(form , callback , {blockPreloader:$('#mySidenavRight')});


    },
    CouponCallback: function (data) {
        var $ = jQuery;



        if (data.data === null) {



            jQuery('div').XzlibModal_setHead(data.message).XzlibModal_setContent(data.messages);
            // Xzlib.Helper.setFancyboxHead(data.message);
            Xzlib.Helper.openFancyboxKonteyner();
            // alert(data.message)
        } else if ("undefined" !== typeof data.data.fancibox_body) {

            $.fancybox.open( data.data.fancibox_body , {
                afterShow: ( instance, current )=>{}
            });
            closeNav();
        }


    },
};


































