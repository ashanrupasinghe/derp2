/**
 * 
 */
$(document).ready(function(){
	/*$('#confirmation').click(function(){
		var url="";
		var conf=confirm("Are you sure,\nproceed an order with this client?");
		if(conf==true){
			 $.get("http://"+ document.domain +"/o/yourAction/param1/param2/");
			 window.location.href  = "http://"+ document.domain +"/direct2door.erp/orders/add/";
		}
		else{
			return;
		}
	});*/
/*	$('#orders-products-id').change(function(){
		alert('fuck you');
		
	});
	*/
	
	// delegate event handler
	$('#order').on('change keyup', 'select[id^=orders-products-id]', function() {
		//alert(this.id);
		var divid=$(this).closest("div.prod-left").parent().attr('id');
		var listItems=""
	    var productId = $('option:selected', this).val();
		//var productId = $('option:selected', this).text();
		
	    //alert(myBaseUrl+"orders/getProductSeller");
	    //alert(productId);
	    
	    $.post(myBaseUrl+"orders/productsuppliersbyid",
	    	    {productId:productId},
	    	    function(data, status){
	    	    	//alert(data);
	    	    	var supplier=JSON.parse(data);    	    	
	    	    	
	    	    	listItems= "<option value=''>select supplier</option>";
	    	        for (var i = 0; i < supplier.length; i++){
	    	          listItems+= "<option value='" + supplier[i].s.id + "'>" + supplier[i].s.firstName+" "+supplier[i].s.lastName+" - "+supplier[i].city.cname + "</option>";
	    	        }
	    	        
	    	        
	    	        //$(this).next().next().html(listItems);
	    	        $("#"+divid).find("select.sup-select").html(listItems);
	    	        $("#"+divid).find("input.packagetype").val("X "+supplier[0].pack.type);
	    	        //$("select#orders-products-id").closest("div.prod-left").next().next().find("select").html(listItems);
	    	        //$("select#orders-products-id").closest("div.prod-left").next().next().find("select").html(listItems);
	    	        //$("#orders-suppliers-id").html(listItems);
	    	    });
	    //alert(this.id);
	   // $(this).closest("div.prod-left").next().next().find("select").html(listItems);
	    
	    /*$.get(myBaseUrl+"orders/productsuppliers", function(data, status){
	        alert("Data: " + data + "\nStatus: " + status);
	    });*/
	    
	    
	    //$(this).next('input#fuck').val(value.split('-')[6]);
	    
	  //count product total price if quantity field not empty
	    var quantity=$("#"+divid).find("input.product-quantity").val();
	    if(quantity){
	    	displayProductPrice(divid,quantity);
	    	displaySubTotalWithTimeout();
	    }
	    
	    
	}).keyup();
	
	//calculationg price
	 
	    $(document).on('focusout','.product-quantity',function() {
	    	/// $(this).css("background-color", "#FFFFCC");
	    	 var divid=$(this).closest("div").parent().parent().attr('id');
	    	 var quantity=$(this).val();
	    	 
	    	 //var productammount="prpayaring";
	    	 
	    	 displayProductPrice(divid,quantity);
	//    	 alert($('input[name="product_price[]"]').length);
	    	 displaySubTotalWithTimeout();
	    
	    	 
	    	 
	    	 
	    });
	    
		
	    
	    $(document).on('focusout','.product-quantity-x',function() {
	    	//alert('xxxx');
	    	/// $(this).css("background-color", "#FFFFCC");
	    	 var divid=$(this).closest("div").parent().attr('id');
	    	 //alert(divid);
	    	 var quantity=$(this).val();
	    	 //alert(quantity);
	    	 
	    	 //var productammount="prpayaring";
	    	 
	    	 displayProductPrice2(divid,quantity);
	//    	 alert($('input[name="product_price[]"]').length);
	    	 displaySubTotalWithTimeout();
	    
	    	 
	    	 
	    	 
	    });
	    //show next sibbling fields
	    $(document).on('focusin','.product-quantity-x',function() {	
	    	//alert("pakayoo");
	    	$(this).parent().parent().prev().children(".prod-left").siblings().show();
	    	//$("div#x-"+element.value+" div.prod-left").siblings().hide();
	    	
	    });
	    
	   /* $( ".btnRemove" ).click(function() {
	    	alert('xxxx');
	    	//displaySubTotalWithTimeout();
	    	});*/
	    
	    
	    function displayProductPrice(divid,quantity){
	    	//alert(divid+" : "+quantity);
	    	//if(quantity!="" && productId!=""){
	    	var productId=$("#"+divid).find("select#orders-products-id").val();
	    	if(quantity && productId){
		    	 $.post(myBaseUrl+"orders/singlecal",
		 	    	    {"productId":productId,"quantity":quantity},
		 	    	    function(data, status){
		 	    	    	//alert(data);//{"productQuantity":5,"productPrice":100,total":500}
		 	    	    	var productTotalPrice=JSON.parse(data);
		 	    	    	var totalstring=productTotalPrice['productPrice']+" X "+productTotalPrice['productQuantity']+" = "+productTotalPrice['total'];
		 	    	    	$("#"+divid).find("input.product-ammount").val(totalstring);
		 	    	    	$("#"+divid).find("input.product-ammount-hidden").val(productTotalPrice['total']);
		 	    	    	
		 	    	    });
		    	 }
	    }
	    
	    function displayProductPrice2(divid,quantity){
	    	//alert(divid+" : "+quantity);
	    	//if(quantity!="" && productId!=""){
	    	var productId=$("#"+divid).find("select.orders-products-id").val();
	    	//alert(productId);
	    	if(quantity && productId){
		    	 $.post(myBaseUrl+"orders/singlecal",
		 	    	    {"productId":productId,"quantity":quantity},
		 	    	    function(data, status){
		 	    	    	//alert(data);//{"productQuantity":5,"productPrice":100,total":500}
		 	    	    	var productTotalPrice=JSON.parse(data);
		 	    	    	var totalstring=productTotalPrice['productPrice']+"X"+productTotalPrice['productQuantity']+" = "+productTotalPrice['total'];
		 	    	    	$("#"+divid).find("input.product-ammount").val(totalstring);
		 	    	    	$("#"+divid).find("input.product-ammount-hidden").val(productTotalPrice['total']);
		 	    	    	
		 	    	    });
		    	 }
	    }
	    
	    
	    
	    function displaySubTotal(){
	    	var productPrices = $('input[name="product_price[]"]');
	    	 var totalPrice=0;
	    	 for(var i = 0; i < productPrices.length; i++){
	    		 totalPrice+=parseInt(($(productPrices[i]).val()));
	    		}
	    	//tax, discount
	    	 var tax_p=0;//persantage 10
	    	 var discount_p=0;//persantage 5
	    	 var tax=(totalPrice*tax_p)/100;
	    	 //var discount=(totalPrice*discount_p)/100;
	    	 var discount=$('#direct-discount').val();
	    	 if(!discount){
	    		 discount=0;
	    	 }
	    	 var total=(totalPrice+tax-discount);
	    	// alert(discount);
	    	 $("#subtotal").val(totalPrice);
	    	 $("#tax").val(tax); 
	    	 $("#discount").val(discount); 
	    	 $("#total").val(total); 
	    	  
	    }
	    
	    function displaySubTotalWithTimeout(){
	    	setTimeout(displaySubTotal, 2000);
	    }
	    
	    $(document).on("pageload",function(){
	    	  alert("pageload event fired!");
	    	});
	    
	    /*multiple select dropdown*/

		  $('#product-list').on('change', function(e) {
		    var cacheEle = $('.dynamic-fields-product-list');
		    
		    $(this).find('option').each(function(index, element) {
		      if (element.selected) {
		    	  var hiddenClass="showclass";
		    	  //var hiddenClass="hiddenclass";
		    	  if(cacheEle.find('div.prod').length==0){
		    		  hiddenClass="showclass"
		    	  }
		        if (cacheEle.find('input[name="product_name\\[' + element.value + '\\]"]').length == 0) {
		        
		        	var productId=element.value;
		            $.post(myBaseUrl+"orders/productsuppliersbyid",
		            	    {"productId":productId},
		            	    function(data, status){
		            	    	//alert(data);
		            	    	var supplier=JSON.parse(data);    	    	
		            	    	var listItems="";
/*		            	    	listItems= "<option value=''>select supplier</option>";*/
		            	        for (var i = 0; i < supplier.length; i++){
		            	          listItems+= "<option value='" + supplier[i].s.id + "'>" + supplier[i].s.firstName+" "+supplier[i].s.lastName+" - "+supplier[i].city.cname + "</option>";
		            	        }
		            	        
		            	        
		      		          var row='<div class="group col-lg-12 col-md-12 columns prod" id="x-'+element.value+'" style="padding-left:0px; padding-right:0px;">'+
			          			'<div class="col-lg-3 col-md-3 columns prod-left form-group">'+
			          				'<input type="text" class="form-control" name="product_name[' + element.value + ']" value="' + element.text + '" disabled>'+
			          				'<select name="product_name[]" class="form-control select2-display-none orders-products-id"><option value="'+element.value+'" selected>'+element.text+'</option></select>'+
			          			'</div>'+
			          			'<div class="col-lg-1 col-md-1 columns form-group">'+
			          				'<input name="product_quantity[]" class="form-control product-quantity-x '+hiddenClass+'" required="required" type="text">'+
			          			'</div>'+
			          			'<div class="col-lg-2 col-md-2 columns form-group">'+
			          				'<input name="Package" disabled="disabled" class="form-control packagetype '+hiddenClass+'" id="" type="text" value="'+' X '+supplier[0].pack.type+'">'+
			          			'</div>'+
			          			'<div class="col-lg-3 col-md-3 columns form-group">'+
			          				'<input name="Ammount" disabled="disabled" class="form-control product-ammount '+hiddenClass+'" id="" type="text">'+
			          				'<input name="product_price[]" disabled="disabled" class="form-control product-ammount-hidden" id="" value="0" type="hidden">'+
			          			'</div>'+
			          			'<div class="col-lg-3 col-md-3 columns sup-right form-group">'+
			          				'<select id="sup-'+element.value+'" name="product_supplier[]" class="form-control orders-suppliers-id '+hiddenClass+'">'+listItems+'</select>'+
			          			'</div>'+
			          		   '</div>';
			          cacheEle.prepend(row);
			          $('select').select2();
			          $("div.prod-left span.select2-container").css({"display": "none"});
			        if(cacheEle.find('div.prod').length>1){
			        	  $("div#x-"+element.value+" div.prod-left").siblings().hide();
			        	  var previusQTY=$("div#x-"+element.value).next().find("input.product-quantity-x").val();
			        	  if(previusQTY){
			        		  $("div#x-"+element.value+" div.prod-left").siblings().show();
			        	  }
		            }
			        /*var numOfQtyPlusOne=parseInt($('input.product-quantity-x:visible').length)+parseInt(1); 
			        
			        if(cacheEle.find('div.prod').length==2 && !numOfQtyPlusOne){
			        	$("div#x-"+element.value+" div.prod-left").siblings().show();
			        } */
				          //var y =parseInt($('input.product-quantity-x:visible').length);
			       /* else if(cacheEle.find('div.prod').length==numOfQtyPlusOne){
		            			$("div#x-"+element.value+" div.prod-left").siblings().show();
			        		 // alert("hukk");
			        } */ 

			        //check priveus qty
			        
			        
			          
			          
			          
			          
			    
			          
/*			          if(cacheEle.find('div.prod').length>1){
			          $('span#select2-sup-'+element.value+'-container').parent().hide();
			          }*/
		            	        
		            	    });
		        	
		        	
		        	
		       // alert(element.value);
		       //   var suppliers=getSupplierList(element.value);

		          //cacheEle.append('<select name="product_name[]"><option value="'+element.value+'">element.text</option></select>')
		        }
		      }  else {
		      
		      //alert(element.value+"-fuck");
		        var x=cacheEle.find('input[name="product_name\\[' + element.value + '\\]"]');
		        x.parent().parent().remove();
		        
		      }
		    });
		  });
/*get a supplier list for a product id*/		  
function getSupplierList(productId){
    $.post(myBaseUrl+"orders/productsuppliersbyid",
    	    {"productId":productId},
    	    function(data, status){
    	    	//alert(data);
    	    	var supplier=JSON.parse(data);    	    	
    	    	
    	    	listItems= "<option value=''>select supplier</option>";
    	        for (var i = 0; i < supplier.length; i++){
    	          listItems+= "<option value='" + supplier[i].s.id + "'>" + supplier[i].s.firstName+" "+supplier[i].s.lastName+" - "+supplier[i].city.cname + "</option>";
    	        }
    	        
    	        
    	        alert(listItems);

    	        
    	    });
}		

/*$('li#notify').click(function(){
	alert("pako");
})
*/
$( ".notify-seen" ).mouseover(function() {	  
	var notificationId=$(this).attr('id');
	
	   $.post(myBaseUrl+"UserNotifications/updateSeen",
	    	    {"id":notificationId},
	    	    function(data, status){
	    	    	var newdata=JSON.parse(data); 
	    	    		newdata['notificationCount'];	    	    		
	    	        $("#notify a span.badge").html(newdata['notificationCount'])
	    	        $("li#"+notificationId+" a span.message span.see-sow").html('&#40;Seen&#41;');
	    	        $("tr#"+notificationId+" td.see-sow").html('Seen');
	    	    });
	
	
	});

/*
 * change total value when adding discount
 * */
$(document).on('focusout','#direct-discount',function() {
	 displaySubTotalWithTimeout();
	 
});


	    
	    
	
});


