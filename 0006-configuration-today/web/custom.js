

function checkDeliveryDate(selectedDate) {
    jQuery.ajax({
        type: 'post',
        url: myBaseUrl + 'cart/verifyDeliverySlots',
        dataType: 'json',
        data: {
            selected_date: selectedDate
        },
        success: function (response) {
            console.log(response);
            if (response.status == '200') {
                jQuery('#delivery_message').hide();
                jQuery('#delivery_time').show();
                var jsonData = response.time_slots;
                var appendata = '';
                for (var key in jsonData) {
                    appendata += "<option value = '" + key + " '>" + jsonData[key] + " </option>";
                }
                jQuery('#delivery_time').empty().append(appendata);
            } else {
                jQuery('#delivery_time').hide();
                jQuery('#delivery_message').html(response.message);
            }

        }
    });
}

jQuery(document).ready(function () {

    //Timepicker:::http://jdewit.github.io/bootstrap-timepicker/
    /*jQuery(".delivery_time").timepicker({
     showInputs: false,
     showMeridian:false,
     defaultTime:'current'
     });*/

    //Date picker
    var dateToday = new Date();
	/** Days to be disabled as an array */
    var disableddates=[];
	
	jQuery.ajax({
        type: 'post',
        url: myBaseUrl + 'boolian-configurations/getboolianconfiguration',
        dataType: 'json',  
		data: {
            key: 'enable_today'
        },		
        success: function (response) { 			
            if (response.value == 0) {
				dateToday.setDate(dateToday.getDate() + 1);
				
               
            }
        }
    });
	
	jQuery.ajax({
        type: 'post',
        url: myBaseUrl + 'boolian-configurations/getunavailabledates',
        dataType: 'json',  
		data: {
            key: 'enable_today'
        },		
        success: function (response) {         	
        	disableddates=response.dates;
        },
        async: false
    });
	
	   function DisableSpecificDates(date) {
     
     var m = date.getMonth();
     var d = date.getDate();
     var y = date.getFullYear();
     
     
     
     // First convert the date in to the mm-dd-yyyy format 
     // Take note that we will increment the month count by 1 
     var currentdate = y +'-'+ (m + 1) + '-' + (d<10 ? '0' : '') + d;
          
     // We will now check if the date belongs to disableddates array 
     for (var i = 0; i < disableddates.length; i++) {
     
     // Now check if the current date is in disabled dates array. 
     if (jQuery.inArray(currentdate, disableddates) != -1 ) {
     return [false];
     } 
     }
     
     // In case the date is not present in disabled array, we will now check if it is a weekend. 
     // We will use the noWeekends function
    /* var weekenddate = jQuery.datepicker.noWeekends(date);
     return weekenddate; */
     
     var day = date.getDay();
     return [(day != 0), ''];
     
    }
	

    jQuery("#datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        autoclose: true,
        minDate: dateToday,
        beforeShowDay: DisableSpecificDates,
        onSelect: function (selectedDate) {
            checkDeliveryDate(selectedDate);
        }
    });

    if (jQuery("#address_id").val() === "") {
        jQuery("#billing-new-address-form").css("display", "block");

    }
    jQuery("#address_id").change(function () {
        if (jQuery("#address_id").val() === "") {
            jQuery("#billing-new-address-form").css("display", "block");

        } else {
            jQuery("#billing-new-address-form").css("display", "none");
        }

    });

//
    jQuery(".continue").click(function () {
        if (jQuery('#checkout-step-billing').is(":visible")) {
            //console.log( jQuery("#co-billing-form").serialize() );	
            jQuery(".back").hide();
            current_fs = jQuery('#checkout-step-billing');
            next_fs = jQuery('#checkout-step-shipping');
            current_collored = jQuery("#opc-billing");
            next_collored = jQuery("#opc-shipping");

            jQuery("div#err_1").empty();
            if (jQuery("#address_id").val() !== "") {
                var address_id = jQuery("#address_id").val();
                jQuery("#billing-please-wait").show();
                jQuery.ajax({
                    type: 'post',
                    url: myBaseUrl + 'cart/updateAddress',
                    dataType: 'json',
                    data: {
                        address_id: address_id
                    },
                    success: function (response) {
                        jQuery(".back").show();
                        jQuery("#billing-please-wait").hide();
                        // alert(JSON.stringify(response.result));	
                        var address_list = addressDropdown(response.result);
                        //alert(address_list);
                        jQuery("#address_id").empty();
                        jQuery("#address_id").append(address_list);
                        next_fs.show();
                        current_fs.hide();
                        current_collored.removeClass("active");
                        next_collored.addClass("active");
                    },
                    error: function (xhr, status) {
                        var err = eval("(" + xhr.responseText + ")");
                        jQuery(".back").show();
                        jQuery("#billing-please-wait").hide();
                        jQuery("div#err_1").append("<p>Something went wrong: " + err.message + "</p>");
                    }
                });
            } else {
                var street_number = jQuery("#street_number").val();
                var street_address = jQuery("#street_address").val();
                var city = jQuery("#city").val();
                var country = jQuery("#country").val();
                var phone_number = jQuery("#phone_number").val();

                if (street_number.trim() == "" || street_address.trim() == "" || city.trim() == "" || phone_number.trim() == "") {//sudha
                    jQuery('#checkout_address_error').show();//sudha
                } else {//sudha

                    jQuery("#billing-please-wait").show();
                    jQuery('#checkout_address_error').hide();//sudha
                    jQuery.ajax({
                        type: 'post',
                        url: myBaseUrl + 'cart/addAddress',
                        dataType: 'json',
                        data: {
                            street_number: street_number,
                            street_address: street_address,
                            city: city,
                            country: country,
                            phone_number: phone_number
                        },
                        success: function (response) {
                            jQuery(".back").show();
                            jQuery("#billing-please-wait").hide();
                            //alert(JSON.stringify(response));
                            var address_list = addressDropdown(response.result);
                            // alert(address_list);
                            jQuery("#address_id").empty();
                            jQuery("#address_id").append(address_list)

                            next_fs.show();
                            current_fs.hide();
                            current_collored.removeClass("active");
                            next_collored.addClass("active");
                        },
                        error: function (xhr, status) {
                            var err = eval("(" + xhr.responseText + ")");
                            jQuery(".back").show();
                            jQuery("#billing-please-wait").hide();
                            //jQuery("div#err_1").empty();
                            jQuery("div#err_1").append("<p>Something went wrong: " + err.message + "</p>");
                        }
                    });
                }

            }//sudha


        } else if (jQuery('#checkout-step-shipping').is(":visible")) {
            jQuery(".back").hide();
            current_fs = jQuery('#checkout-step-shipping');
            next_fs = jQuery('#checkout-step-review');
            current_collored = jQuery("#opc-shipping");
            next_collored = jQuery("#opc-review");
            //jQuery("#shipping-please-wait").show();//sudha
            jQuery("div#err_2").empty();

            var delivery_date = jQuery(".delivery_date").val();
            var delivery_time = jQuery(".delivery_time").val();
            /*var delivery_date="05/31/2017";
             var delivery_time="12:45";*/

            jQuery("#shipping-please-wait").show();

            jQuery.ajax({
                type: 'post',
                url: myBaseUrl + 'cart/updateDeliveryTime',
                dataType: 'json',
                data: {
                    delivery_date: delivery_date,
                    delivery_time: delivery_time
                },
                success: function (response) {
                    jQuery(".back").show();
                    jQuery("#shipping-please-wait").hide();
                    /*alert(JSON.stringify(response)); */
                    next_fs.show();
                    current_fs.hide();
                    current_collored.removeClass("active");
                    next_collored.addClass("active");
                },
                error: function (xhr, status) {
                    var err = eval("(" + xhr.responseText + ")");
                    jQuery("#shipping-please-wait").hide();
                    jQuery(".back").show();
                    //jQuery("div#err_2").empty();
                    jQuery("div#err_2").append("<p>Something went wrong: " + err.message + "</p>");
                }
            });
        } else if (jQuery('#checkout-step-review').is(":visible")) {
            jQuery(".back").hide();
            current_fs = jQuery('#checkout-step-review');
            current_collored = jQuery("#opc-review");
            jQuery("#review-please-wait").show();
            jQuery("div#err_3").empty();
			payment_method = jQuery("input[name='payment_method']:checked"). val();
            jQuery.ajax({
                type: 'post',
				data:{
                	payment_method:payment_method
                	},
                url: myBaseUrl + 'cart/completeCheckout',
                dataType: 'json',
                success: function (response) {
                    jQuery("#review-please-wait").hide();
                    document.getElementById("fl-mini-cart-content").innerHTML = "";
                    document.getElementById("total_items").innerHTML = 0;
					jQuery("#cd-cart-container-footer-cart").addClass("empty");
					//empty
                    //if response equalt o success then get order total and call to the payment gateway
                    var success_message	="";
                    if(payment_method==3){
                    	success_message="<p style='color:#8a6d3b;'>Thank you for your order, we will mail you a payment link once the order is processed</p>";
                    }else if(payment_method==2){
						success_message="<p style='color:#8a6d3b;'>We accept Master & Visa card payment on delivery. The delivery rider will be in possession of a card machine</p>";
					}
					success_message+="<p style='color:#3c763d;'>Your order has been placed successfully. You will receive an order confirmation mail shortly. Please contact us on 0117 556600 for any queries. </p>";
                    jQuery("div#succsess_3").append(success_message);
					//disable buttons
					jQuery(".continue").css("display", "none");
					//disable links
					jQuery("a.back").css("display", "none");
					//show dashboard button #go-to-dashboard
					jQuery("#go-to-dashboard").css("display", "");
                    /*
					setTimeout(function () {// wait for 5 secs(2)
                        location.href = myBaseUrl + 'user/dashboard';
                    }, 2000);
					*/
                    /*alert(JSON.stringify(response)); */
                    //next_fs.show(); 
                    //current_fs.hide();
                    //current_collored.removeClass("active");
                    //next_collored.addClass("active");
                },
                error: function (xhr, status) {
                    //alert(JSON.stringify(xhr));
                    var err = eval("(" + xhr.responseText + ")");
                    jQuery("#review-please-wait").hide();
                    jQuery(".back").show();
                    //jQuery("div#err_3").empty();
                    jQuery("div#err_3").append("<p>Something went wrong: " + err.message + "</p>");
                }
            });

        }

        /*next_fs.show(); 
         current_fs.hide();
         current_collored.removeClass("active");
         next_collored.addClass("active");*/
    });

    jQuery(".back").click(function () {
        if (jQuery('#checkout-step-review').is(":visible")) {
            current_fs = jQuery('#checkout-step-review');
            back_fs = jQuery('#checkout-step-shipping');
            current_collored = jQuery("#opc-review");
            next_collored = jQuery("#opc-shipping");

        } else if (jQuery('#checkout-step-shipping').is(":visible")) {
            current_fs = jQuery('#checkout-step-shipping');
            back_fs = jQuery('#checkout-step-billing');
            current_collored = jQuery("#opc-shipping");
            next_collored = jQuery("#opc-billing");

        }

        back_fs.show();
        current_fs.hide();
        current_collored.removeClass("active");
        next_collored.addClass("active");
    });

    function addressDropdown(responseAddress) {
        var i;
        var len;
        var list = "";
        for (i = 0; i < responseAddress.length; i++) {
            list += "<option value='" + responseAddress[i].id + "'>" + responseAddress[i].address + "</option>";

        }
        list += "<option>New Address</option>";
        return list;
    }
    //remove item from cart
    /*jQuery(".remove-from-cart-jq-function").click(function(){		
     product_id = jQuery(this).parent().find("input[name='product_id']").val();
     alert(product_id);
     
     });*/

    jQuery("body").on("click", ".remove-from-cart-jq-function", function () {
        product_id = jQuery(this).parent().find("input[name='product_id']").val();

        jQuery.confirm({
            title: 'Confirm!',
            content: 'Are you sure you would like to remove this item from the shopping cart?',
            buttons: {
                confirm: function () {
                    jQuery.ajax({
                        type: 'post',
                        url: myBaseUrl + 'cart/deleteproduct',
                        dataType: 'json',
                        data: {
                            product_id: product_id
                        },
                        success: function (response) {

                            if (response.status == 0) {
                                list = "";
                                table = "";
                                Totaltable = "";
                                cd_cart_count="";
                                footer_mini_cart="";
                                cd_mincart_footer_total=response.result.result.total.grand_total;
                                list_ul="";

                                if (response.result.result.cart_size >= 0) {
                                    //document.getElementById("total_items").innerHTML = response.result.cart_size;

                                    list += '<div class="basket">';
                                    list += '<a href="' + myBaseUrl + 'user/cart' + '"><span id="total_items"> ' + response.result.result.cart_size + ' </span></a>';
                                    list += '</div>';
                                    
                                    cd_cart_count='<li>'+response.result.result.cart_size+'</li><li>0</li>';
                                }
                                if (Object.keys(response.result.result.product_list).length > 0) {
                                    //alert(JSON.stringify(response.result.product_list));
                                    var count = 0;
                                    list += '<div class="fl-mini-cart-content" style="display: none;">';
                                    list += '<div class="block-subtitle">';
                                    list += '<div class="top-subtotal" id="top-sub-total">';
                                    list += response.result.result.cart_size + ' items, <span class="price">LKR ' + response.result.result.total.grand_total + '.00</span>';
                                    list += '</div>';
                                    list += '</div>';
                                    list_ul += '<ul class="mini-products-list" id="cart-sidebar">';
                                    footer_mini_cart+='<ul>';
                                    jQuery.each(response.result.result.product_list, function (index, value) {

                                    	list_ul += '<li class="item first last">';
                                    	list_ul += '<div class="item-inner">';
                                    	list_ul += '<a class="product-image" title="' + value.name + '" href="#">';
                                    	list_ul += '<img alt="' + value.name + '" src="' + value.image + '"></a>';
                                    	list_ul += '<div class="product-details">';
                                    	list_ul += '<div class="access">';
                                    	list_ul += '<input type="hidden" value="' + value.id + '" name="product_id" class="">';
                                    	list_ul += '<a class="btn-remove1 remove-from-cart-jq-function" title="Remove This Item" href="#">Remove</a>';
                                    	list_ul += '<a class="btn-edit" title="Edit item" href="#">';
                                    	list_ul += '<i class="icon-pencil"></i><span class="hidden">Edit item</span></a>';
                                    	list_ul += '</div>';

                                    	list_ul += '<strong>' + value.quantity + '</strong> x <span class="price">LKR ' + value.price + '.00</span>';
                                    	list_ul += '<p class="product-name">';
                                    	list_ul += '<a href="product-detail.html">' + value.name + '</a>';
                                    	list_ul += '</p>';
                                    	list_ul += '</div>';
                                    	list_ul += '</div>';
                                    	list_ul += '</li>';
                                    	
                                    	
                                    	footer_mini_cart+='<li class="product">';
                                    	footer_mini_cart+='<div class="product-image">';
                                    	footer_mini_cart+='<a href="#0" title="' + value.name + '"><img src="' + value.image + '" alt="placeholder"></a>';
                                    	footer_mini_cart+='</div>';
                                    	footer_mini_cart+='<div class="product-details">';
                                    	footer_mini_cart+='<h3><a href="#0">' + value.name + '</a></h3>';
                                    	footer_mini_cart+='<span class="price"><strong class="str-qty">'+ value.quantity +' x</strong> LKR ' + value.price + '.00</span>';
                                    	footer_mini_cart+='<div class="actions">';
                                    	footer_mini_cart+='<div class="access"><input value="'+ value.id +'" name="product_id" class="" type="hidden">';
                                    	footer_mini_cart+='<a class="remove-from-cart-jq-function" title="Remove This Item" href="#">Remove</a>';                                    	
                                    	footer_mini_cart+='&nbsp;&nbsp;<a class="edit-product-jq-function" title="Edit item" href="#">Edit</a>';
                                    	footer_mini_cart+='</div>';
                                    	footer_mini_cart+='</div>';
                                    	footer_mini_cart+='</div>';
                                    	footer_mini_cart+='</li>';
                                    	
                                        count++;
                                    });

                                    //document.getElementById("cart-sidebar").innerHTML=list;
                                    //document.getElementById("top-sub-total").innerHTML=response.result.cart_size+' items, <span class="price">LKR '+response.result.total.grand_total+'</span>';




                                    list_ul += '</ul>';
                                    footer_mini_cart+='</ul>';
                                    list += list_ul+'<div class="actions">';
                                    list += '<button class="btn-checkout" title="Checkout" type="button" onClick="location.href=\'' + myBaseUrl + 'order/checkout\'">';
                                    list += '<span>Checkout</span>';
                                    list += '</button>';
                                    list += '</div>';
                                    list += '</div>';
                                }
                                document.getElementById("mini-cart-head").innerHTML = list;
                                document.getElementById("footer_mini_card_body").innerHTML = footer_mini_cart;
                                document.getElementById("cd-mincart-footer-total").innerHTML = cd_mincart_footer_total+'.00';
                                document.getElementById("cd-cart-count").innerHTML = cd_cart_count;
                                if (jQuery("#shopping-cart-table").length) {
                                    //cart table

                                    table += '<input name="form_key" type="hidden" value="EPYwQxF6xoWcjLUr">';
                                    table += '<fieldset>';
                                    table += '<table id="shopping-cart-table" class="data-table cart-table table-striped">';
                                    table += '<colgroup><col width="1"><col><col width="1"><col width="1"><col width="1"><col width="1"><col width="1"></colgroup><thead>';
                                    table += '<tr class="first last"><th rowspan="1">&nbsp;</th><th rowspan="1"><span class="nobr">Product Name</span></th><th rowspan="1"></th><th class="a-center" colspan="1"><span class="nobr">Unit Price</span></th><th rowspan="1" class="a-center">Qty</th><th class="a-center" colspan="1">Subtotal</th></tr>';
                                    table += '</thead><tfoot><tr class="first last"><td colspan="50" class="a-right last"><button type="button" title="Continue Shopping" class="button btn-continue" onClick=""><span><span>Continue Shopping</span></span></button><button type="submit" name="update_cart_action" value="update_qty" title="Update Cart" class="button btn-update"><span><span>Update Cart</span></span></button><button type="submit" name="update_cart_action" value="empty_cart" title="Clear Cart" class="button btn-empty" id="empty_cart_button"><span><span>Clear Cart</span></span></button></td></tr></tfoot>';
                                    table += '<tbody>';
                                    productcount = 1;
                                    jQuery.each(response.result.result.product_list, function (index, value) {
                                        trclass = "odd";
                                        if (productcount == 1) {
                                            trclass += " first last"
                                        }
                                        if (Object.keys(response.result.result.product_list).length == productcount) {
                                            trclass += " last"
                                        }
                                        table += '<tr class="' + trclass + '">'
                                        table += '<td class="image hidden-table"><a href="product-detail.html" title="' + value.name + '" class="product-image"><img src="' + value.image + '" width="75" alt="' + value.name + '"></a></td>';
                                        table += '<td>';
                                        table += '<h2 class="product-name">';
                                        table += '<a href="product-detail.html">' + value.name + '</a>';
                                        table += '</h2>';
                                        table += '</td>';
                                        table += '<td class="a-center hidden-table">';
                                        table += '<input value="' + value.id + '" name="product_id" class="" type="hidden"> ';
                                        table += '<a href="#" class="edit-bnt edit-product-jq-function" title="Edit item parameters"></a>';
                                        table += '</td>';
                                        table += '<td class="a-right hidden-table">';
                                        table += '<span class="cart-price">';
                                        table += '<span class="price">LKR ' + value.price + '</span>';
                                        table += '</span>';
                                        table += '</td>';
                                        table += '<td class="a-center movewishlist">';
                                        table += '<input name="cart[26340][qty]" value="' + value.quantity + '" size="4" title="Qty" class="input-text qty" maxlength="12">';
                                        table += '</td>';
                                        table += '<td class="a-right movewishlist">';
                                        table += '<span class="cart-price">';
                                        table += '<span class="price">LKR ' + value.total + '</span>';
                                        table += '</span>';
                                        table += '</td>';
                                        table += '<td class="a-center last">';
                                        table += '<input type="hidden" value="' + value.id + '" name="product_id" class="">';
                                        table += '<a href="#" title="Remove item" class="button remove-item remove-from-cart-jq-function"><span><span>Remove item</span></span></a></td>';
                                        table += '</tr> ';
                                        productcount++;
                                    });

                                    table += '</tbody></table></fieldset>';

                                    Totaltable += '<colgroup><col>';
                                    Totaltable += '<col width="1">';
                                    Totaltable += '</colgroup><tfoot>';
                                    Totaltable += '<tr>';
                                    Totaltable += '<td style="" class="a-left" colspan="1"><strong>Grand Total</strong></td>';
                                    Totaltable += '<td style="" class="a-right"><strong><span class="price">LKR ' + response.result.result.total.grand_total + '</span></strong></td>';
                                    Totaltable += '</tr>';
                                    Totaltable += '</tfoot>';
                                    Totaltable += '<tbody>';
                                    Totaltable += '<tr>';
                                    Totaltable += '<td style="" class="a-left" colspan="1"> Subtotal</td>';
                                    Totaltable += '<td style="" class="a-right"><span class="price">LKR ' + response.result.result.total.sub_total + '</span></td>';
                                    Totaltable += '</tr>';
                                    Totaltable += '<tr>';
                                    Totaltable += '<td style="" class="a-left" colspan="1">  Tax    </td>';
                                    Totaltable += '<td style="" class="a-right"> <span class="price">LKR ' + response.result.result.total.tax + '</span></td>';
                                    Totaltable += '</tr>';
                                    Totaltable += '<tr>';
                                    Totaltable += '<td style="" class="a-left" colspan="1">     Discount    </td>';
                                    Totaltable += '<td style="" class="a-right"><span class="price">LKR ' + response.result.result.total.discount + '</span>    </td>';
                                    Totaltable += '</tr>';
                                    Totaltable += '<tr>';
                                    Totaltable += '<td style="" class="a-left" colspan="1">        Counpon Value    </td>';
                                    Totaltable += '<td style="" class="a-right"><span class="price">LKR ' + response.result.result.total.counpon_value + '</span>    </td>';
                                    Totaltable += '</tr>';
                                    Totaltable += '</tbody>';
                                    if (Object.keys(response.result.result.product_list).length > 0) {
                                        document.getElementById("get-checkot-table-form").innerHTML = table;
                                    } else {
                                        document.getElementById("get-checkot-table-form").innerHTML = "<p class='nothing-found'>Your Cart Is Empty</p>";
                                    }
                                    document.getElementById("shopping-cart-totals-table").innerHTML = Totaltable;

                                }
                                //alert(list);


                            }

                            jQuery.alert(response.message);
                        },
                        error: function (xhr, status) {
                            //err = eval("(" + xhr.responseText + ")");
                            //jQuery("div#err_3").append("<p>Something went wrong: "+err.message+"</p>");
                            jQuery.alert("Something went wrong: " + JSON.stringify(xhr));
                        }
                    });
                },
                cancel: function () {
                    //jQuery.alert('CANCEL');
                    return;
                }
            }
        });
    });


    jQuery("body").on("click", ".edit-product-jq-function", function () {
        product_id = jQuery(this).parent().find("input[name='product_id']").val();
        hideAddProductAllResponse();
        jQuery.ajax({
            type: 'post',
            url: myBaseUrl + 'cart/quickedit',
            dataType: 'json',
            data: {
                product_id: product_id
            },
            success: function (response) {
	
                jQuery("#popup-quick-view-edit #quick_edit_h1").text(response.name);
                jQuery("#popup-quick-view-edit #quick_edit_price").text("LKR " + response.price);
                jQuery("#popup-quick-view-edit #quick_edit_package").text(response.package_type.type);
                jQuery("#popup-quick-view-edit #quick_edit_description").text(response.description);
                jQuery("#popup-quick-view-edit #quick_edit_qty").val(response.cart_products[0].qty);
                jQuery("#popup-quick-view-edit #product_id").val(response.id);
                jQuery("#popup-quick-view-edit #quick_view_img").attr('src', response.image);
				var organic_categories = [15,16,17,18,101,102,103,104,185];	
					if(jQuery.inArray( response.category_id, organic_categories ) !=-1){
						jQuery("#is_organic").css({'display': ''});
					}else{
						jQuery("#is_organic").css({'display': 'none'});
					}	
                //alert(JSON.stringify(response.cart_products[0].qty));

                jQuery("#popup-quick-view-edit").css({'display': 'block'});
                jQuery("#fade").css({'display': 'block'});


            },
            error: function (xhr, status) {
                jQuery.alert("Something went wrong: " + JSON.stringify(xhr));
            }
        });






    });
    jQuery("body").on("click", ".close-quic-edit", function () {
        jQuery("#popup-quick-view-edit").css({'display': 'none'});
        jQuery("#fade").css({'display': 'none'});
    });


    /*reorder */
    jQuery("body").on("click", ".link-reorder", function () {
        order_id = jQuery(this).parent().find("input[name='product_id']").val();

        jQuery.confirm({
            title: 'Reorder',
            content: 'confirm to reorder',
            buttons: {
                confirm: function () {

                    jQuery.ajax({
                        type: 'post',
                        url: myBaseUrl + 'order/reorder',
                        dataType: 'json',
                        data: {
                            order_id: order_id
                        },
                        success: function (response) {
                            if (response.status == 0) {
                                list = "";

                                if (response.result.cart_size >= 0) {
                                    //document.getElementById("total_items").innerHTML = response.result.cart_size;

                                    list += '<div class="basket">';
                                    list += '<a href="' + myBaseUrl + 'user/cart' + '"><span id="total_items"> ' + response.result.cart_size + ' </span></a>';
                                    list += '</div>';
                                }
                                if (Object.keys(response.result.product_list).length > 0) {
                                    //alert(JSON.stringify(response.result.product_list));
                                    var count = 0;
                                    list += '<div class="fl-mini-cart-content" style="display: none;">';
                                    list += '<div class="block-subtitle">';
                                    list += '<div class="top-subtotal" id="top-sub-total">';
                                    list += response.result.cart_size + ' items, <span class="price">LKR ' + response.result.total.grand_total + '.00</span>';
                                    list += '</div>';
                                    list += '</div>';
                                    list += '<ul class="mini-products-list" id="cart-sidebar">';
                                    jQuery.each(response.result.product_list, function (index, value) {

                                        list += '<li class="item first last">';
                                        list += '<div class="item-inner">';
                                        list += '<a class="product-image" title="' + value.name + '" href="#">';
                                        list += '<img alt="' + value.name + '" src="' + value.image + '"></a>';
                                        list += '<div class="product-details">';
                                        list += '<div class="access">';
                                        list += '<input type="hidden" value="' + value.id + '" name="product_id" class="">';
                                        list += '<a class="btn-remove1 remove-from-cart-jq-function" title="Remove This Item" href="#">Remove</a>';
                                        list += '<a class="btn-edit" title="Edit item" href="#">';
                                        list += '<i class="icon-pencil"></i><span class="hidden">Edit item</span></a>';
                                        list += '</div>';

                                        list += '<strong>' + value.quantity + '</strong> x <span class="price">LKR ' + value.price + '.00</span>';
                                        list += '<p class="product-name">';
                                        list += '<a href="product-detail.html">' + value.name + '</a>';
                                        list += '</p>';
                                        list += '</div>';
                                        list += '</div>';
                                        list += '</li>';

                                        count++;
                                    });
                                    list += '</ul>';
                                    list += '<div class="actions">';
                                    list += '<button class="btn-checkout" title="Checkout" type="button" onClick="location.href=\'' + myBaseUrl + 'order/checkout\'">';
                                    list += '<span>Checkout</span>';
                                    list += '</button>';
                                    list += '</div>';
                                    list += '</div>';
                                }
                                document.getElementById("mini-cart-head").innerHTML = list;
                            }
                            jQuery.alert(response.message);
                        },
                        error: function (xhr, status) {
                            jQuery.alert("Something went wrong: " + JSON.stringify(xhr));
                        }
                    });
                },
                cancel: function () {
                    return;
                }
            }
        });
    });

    //type ahead script
	/*
    jQuery('input.search-product').typeahead({
        source: function (query, process) {
            objects = [];
            map = {};
            //alert(query);
            jQuery.ajax({
                type: 'post',
                url: myBaseUrl + 'products/autocomplete',
                dataType: 'json',
                data: {
                    query: query
                },
                success: function (data, status, xhr) {
                    jQuery.each(data, function (i, object) {
                        map[object.name] = object;
                        objects.push(object.name);
                    });

                    return process(objects);
                },
                error: function (xhr, status) {
                    jQuery.alert("something went wrong");
                }
            });


        },
        updater: function (item) {
            window.location = myBaseUrl + 'product/' + map[item].slug;
            return item;



        }

    });
*/	
	jQuery("body").on("click", ".search-btn", function () {
	var search = jQuery('#search-product').val();
		if(search){
		    window.location = myBaseUrl + 'products/search?key=' + search;
		}
		else{
		return;
		}
	
	});
	 
    jQuery('#search-product').keypress(function(e){
        if(e.which == 13){//Enter key pressed			    
				jQuery('.search-btn').click();//Trigger search button click event			
        }
    });



    jQuery("body").on("click", ".remove-from-wishlist-jq-function", function () {
        product_id = jQuery(this).parent().find("input[name='product_id']").val();

        jQuery.confirm({
            title: 'Confirm!',
            content: 'Are you sure you would like to remove this item from the Wishlist?',
            buttons: {
                confirm: function () {
                    jQuery.ajax({
                        type: 'post',
                        url: myBaseUrl + 'user/deletewishlistitem',
                        dataType: 'json',
                        data: {
                            product_id: product_id
                        },
                        success: function (response) {

                            if (response.status == 0) {
                                list = "";
                                table = "";
                                Totaltable = "";

                                if (response.result.result.cart_size >= 0) {
                                    //document.getElementById("total_items").innerHTML = response.result.cart_size;

                                    list += '<div class="basket">';
                                    list += '<a href="' + myBaseUrl + 'user/wishlist' + '"><span id="total_items"> ' + response.result.result.cart_size + ' </span></a>';
                                    list += '</div>';
                                }

                                document.getElementById("mini-wishlist-head").innerHTML = list;
                                if (jQuery("#wishlist-table").length) {
                                    //cart table

                                    table += '<input name="form_key" type="hidden" value="EPYwQxF6xoWcjLUr">';
                                    table += '<fieldset>';
                                    table += '<table id="wishlist-table" class="data-table wishlist-table table-striped">';
                                    table += '<colgroup><col width="1"><col><col width="1"><col width="1"><col width="1"><col width="1"><col width="1"></colgroup><thead>';
                                    table += '<tr class="first last"><th rowspan="1">&nbsp;</th><th rowspan="1"><span class="nobr">Product Name</span></th><th rowspan="1"></th><th class="a-center" colspan="1"><span class="nobr">Unit Price</span></th><th rowspan="1" class="a-center">Qty</th><th class="a-center" colspan="1">Subtotal</th></tr>';
                                    table += '</thead><tfoot><tr class="first last"><td colspan="50" class="a-right last"><button type="button" title="Continue Shopping" class="button btn-continue" onClick=""><span><span>Continue Shopping</span></span></button><button type="submit" name="update_cart_action" value="update_qty" title="Update Cart" class="button btn-update"><span><span>Update Cart</span></span></button><button type="submit" name="update_cart_action" value="empty_cart" title="Clear Cart" class="button btn-empty" id="empty_cart_button"><span><span>Clear Cart</span></span></button></td></tr></tfoot>';
                                    table += '<tbody>';
                                    productcount = 1;
                                    jQuery.each(response.result.result.product_list, function (index, value) {
                                        trclass = "odd";
                                        if (productcount == 1) {
                                            trclass += " first last"
                                        }
                                        if (Object.keys(response.result.result.product_list).length == productcount) {
                                            trclass += " last"
                                        }
                                        table += '<tr class="' + trclass + '">'
                                        table += '<td class="image hidden-table"><a href="product-detail.html" title="' + value.name + '" class="product-image"><img src="' + value.image + '" width="75" alt="' + value.name + '"></a></td>';
                                        table += '<td>';
                                        table += '<h2 class="product-name">';
                                        table += '<a href="product-detail.html">' + value.name + '</a>';
                                        table += '</h2>';
                                        table += '</td>';
                                        table += '<td class="a-center hidden-table">';
                                        table += '<input value="' + value.id + '" name="product_id" class="" type="hidden"> ';
                                        table += '<a href="#" class="edit-bnt edit-product-jq-function" title="Edit item parameters"></a>';
                                        table += '</td>';
                                        table += '<td class="a-right hidden-table">';
                                        table += '<span class="cart-price">';
                                        table += '<span class="price">LKR ' + value.price + '</span>';
                                        table += '</span>';
                                        table += '</td>';
                                        table += '<td class="a-center movewishlist">';
                                        table += '<input name="cart[26340][qty]" value="' + value.quantity + '" size="4" title="Qty" class="input-text qty" maxlength="12">';
                                        table += '</td>';
                                        table += '<td class="a-right movewishlist">';
                                        table += '<span class="cart-price">';
                                        table += '<span class="price">LKR ' + value.total + '</span>';
                                        table += '</span>';
                                        table += '</td>';
                                        table += '<td class="a-center last">';
                                        table += '<input type="hidden" value="' + value.id + '" name="product_id" class="">';
                                        table += '<a href="#" title="Remove item" class="button remove-item remove-from-wishlist-jq-function"><span><span>Remove item</span></span></a></td>';
                                        table += '</tr> ';
                                        productcount++;
                                    });

                                    table += '</tbody></table></fieldset>';


                                    if (Object.keys(response.result.result.product_list).length > 0) {
                                        document.getElementById("get-wishlist-table-form").innerHTML = table;
                                    } else {
                                        document.getElementById("get-wishlist-table-form").innerHTML = "<p class='nothing-found'>Your Wishlist Is Empty</p>";
                                    }


                                }
                                //alert(list);


                            }

                            jQuery.alert(response.message);
                        },
                        error: function (xhr, status) {
                            //err = eval("(" + xhr.responseText + ")");
                            //jQuery("div#err_3").append("<p>Something went wrong: "+err.message+"</p>");
                            jQuery.alert("Something went wrong: " + JSON.stringify(xhr));
                        }
                    });
                },
                cancel: function () {
                    //jQuery.alert('CANCEL');
                    return;
                }
            }
        });


    });

    /*add to wishlist*/
    jQuery('.add-to-wishlist-jq-function').click(function () {
        product_id = jQuery(this).closest('div').find("input[name='product_id']").val();
        //qty=1
        qty = jQuery(this).closest('div').find("input[name='qty']").val();

        jQuery.ajax({
            type: 'post',
            url: myBaseUrl + 'user/addwishlistitem',
            dataType: 'json',
            data: {
                product_id: product_id,
                qty: qty
            },
            success: function (response) {
                //alert(JSON.stringify(response));
                if (response.status == 0) {
                    list = "";
                    table = "";

                    // alert(JSON.stringify(response));

                    if (response.result.cart_size) {
                        //document.getElementById("total_items").innerHTML = response.result.cart_size;

                        list += '<div class="basket">';
                        list += '<a href="' + myBaseUrl + 'user/wishlist' + '"><span id="total_items"> ' + response.result.cart_size + ' </span></a>';
                        list += '</div>';
                    }
                    document.getElementById("mini-wishlist-head").innerHTML = list;
                    //if in cart page,
                    if (jQuery("#wishlist-table").length) {
                        //cart table

                        table += '<input name="form_key" type="hidden" value="EPYwQxF6xoWcjLUr">';
                        table += '<fieldset>';
                        table += '<table id="wishlist-table" class="data-table cart-table table-striped">';
                        table += '<colgroup><col width="1"><col><col width="1"><col width="1"><col width="1"><col width="1"><col width="1"></colgroup><thead>';
                        table += '<tr class="first last"><th rowspan="1">&nbsp;</th><th rowspan="1"><span class="nobr">Product Name</span></th><th rowspan="1"></th><th class="a-center" colspan="1"><span class="nobr">Unit Price</span></th><th rowspan="1" class="a-center">Qty</th><th class="a-center" colspan="1">Subtotal</th></tr>';
                        table += '</thead><tfoot><tr class="first last"><td colspan="50" class="a-right last"><button type="button" title="Continue Shopping" class="button btn-continue" onClick=""><span><span>Continue Shopping</span></span></button><button type="submit" name="update_cart_action" value="update_qty" title="Update Cart" class="button btn-update"><span><span>Update Cart</span></span></button><button type="submit" name="update_cart_action" value="empty_cart" title="Clear Cart" class="button btn-empty" id="empty_cart_button"><span><span>Clear Cart</span></span></button></td></tr></tfoot>';
                        table += '<tbody>';
                        productcount = 1;
                        jQuery.each(response.result.product_list, function (index, value) {
                            trclass = "odd";
                            if (productcount == 1) {
                                trclass += " first last"
                            }
                            if (Object.keys(response.result.product_list).length == productcount) {
                                trclass += " last"
                            }
                            table += '<tr class="' + trclass + '">'
                            table += '<td class="image hidden-table"><a href="product-detail.html" title="' + value.name + '" class="product-image"><img src="' + value.image + '" width="75" alt="' + value.name + '"></a></td>';
                            table += '<td>';
                            table += '<h2 class="product-name">';
                            table += '<a href="product-detail.html">' + value.name + '</a>';
                            table += '</h2>';
                            table += '</td>';
                            table += '<td class="a-center hidden-table">';
                            table += '<input value="' + value.id + '" name="product_id" class="" type="hidden"> ';
                            table += '<a href="#" class="edit-bnt edit-product-jq-function" title="Edit item parameters"></a>';
                            table += '</td>';
                            table += '<td class="a-right hidden-table">';
                            table += '<span class="cart-price">';
                            table += '<span class="price">LKR ' + value.price + '</span>';
                            table += '</span>';
                            table += '</td>';
                            table += '<td class="a-center movewishlist">';
                            table += '<input name="cart[26340][qty]" value="' + value.quantity + '" size="4" title="Qty" class="input-text qty" maxlength="12">';
                            table += '</td>';
                            table += '<td class="a-right movewishlist">';
                            table += '<span class="cart-price">';
                            table += '<span class="price">LKR ' + value.total + '</span>';
                            table += '</span>';
                            table += '</td>';
                            table += '<td class="a-center last">';
                            table += '<input type="hidden" value="' + value.id + '" name="product_id" class="">';
                            table += '<a href="#" title="Remove item" class="button remove-item remove-from-wishlist-jq-function"><span><span>Remove item</span></span></a></td>';
                            table += '</tr> ';
                            productcount++;
                        });

                        table += '</tbody></table></fieldset>';
                        document.getElementById("get-wishlist-table-form").innerHTML = table;
                    }
                }


                jQuery.alert(response.message);

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    });


    jQuery('#forgotpassword').on('click', function () {
        jQuery.ajax({
            type: 'post',
            url: myBaseUrl + 'user/forgotpassword',
            dataType: 'json',
            data: {
                username: jQuery('#forgotpw-username').val()
            },
            success: function (response) {
                console.log(response);
                if (response.status == '200') {
                    jQuery('#forgotpw-message').html(response.message);

                } else {
                    jQuery('#forgotpw-message').html(response.message);
                }

            }
        });
    });

});
//https://craftpip.github.io/jquery-confirm/#ajaxloading
/**
 * 
 */
function addProductResponse(element,type){
	if(type==0){//hide all
		element.find(".product-add-loading").hide();
		element.find(".product-add-success").hide();	
		element.find(".product-add-err").hide();
	}else if(type==1){//show load
		element.find(".product-add-success").hide();
		element.find(".product-add-err").hide();
		element.find(".product-add-loading").show();
	}else if(type==2){//show success
		element.find(".product-add-loading").hide();
		element.find(".product-add-err").hide();
		element.find(".product-add-success").show();
	}else if(type==3){//show error
		element.find(".product-add-loading").hide();
		element.find(".product-add-success").hide();	
		element.find(".product-add-err").show();
	}
}
/**
 * 
 */
function hideAddProductAllResponse(){
	jQuery(".product-add-loading").hide();
	jQuery(".product-add-success").hide();	
	jQuery(".product-add-err").hide();
}

/*xxxxxx mini cart*/
jQuery(document).ready(function($){
	var cartWrapper = $('.cd-cart-container');
	//product id - you don't need a counter in your real project but you can use your real product id
	var productId = 0;

	if( cartWrapper.length > 0 ) {
		//store jQuery objects
		var cartBody = cartWrapper.find('.body')
		var cartList = cartBody.find('ul').eq(0);
		var cartTotal = cartWrapper.find('.checkout').find('span');
		var cartTrigger = cartWrapper.children('.cd-cart-trigger');
		var cartCount = cartTrigger.children('.count')
		/*var addToCartBtn = $('.cd-add-to-cart');*/
		var addToCartBtn = $('.add-to-cart-jq-function');
		//var undo = cartWrapper.find('.undo');
		//var undoTimeoutId;

		//add product to cart
		addToCartBtn.on('click', function(event){
			event.preventDefault();
			//alert("xasa");
			addToCart($(this));
		});

		//open/close cart
		cartTrigger.on('click', function(event){
			event.preventDefault();
			toggleCart();
		});

		//close cart when clicking on the .cd-cart-container::before (bg layer)
		cartWrapper.on('click', function(event){
			if( $(event.target).is($(this)) ) toggleCart(true);
		});
		
	}

	function toggleCart(bool) {
		var cartIsOpen = ( typeof bool === 'undefined' ) ? cartWrapper.hasClass('cart-open') : bool;
		
		if( cartIsOpen ) {
			cartWrapper.removeClass('cart-open');
			//reset undo
			clearInterval(undoTimeoutId);
			undo.removeClass('visible');
			cartList.find('.deleted').remove();

			setTimeout(function(){
				cartBody.scrollTop(0);
				//check if cart empty to hide it
				if( Number(cartCount.find('li').eq(0).text()) == 0) cartWrapper.addClass('empty');
			}, 500);
		} else {
			cartWrapper.addClass('cart-open');
		}
	}

	function addToCart(trigger) {
		var cartIsEmpty = cartWrapper.hasClass('empty');
		//update cart product list
		//addProduct();
		//update number of items 
		//updateCartCount(cartIsEmpty);
		//update total price
		//updateCartTotal(trigger.data('price'), true);
		//show cart
		cartWrapper.removeClass('empty');
	}
	
	

});


