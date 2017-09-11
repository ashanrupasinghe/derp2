<div class="page-heading">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-title">
                    <h2>Checkout</h2>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- BEGIN Main Container col2-right -->
<div class="main-container col2-right-layout">
    <div class="main container">
        <div class="row">
            <section class="col-main col-sm-9 wow bounceInUp animated animated"
                     style="visibility: visible;">
                <?php 
                if($cart_total['grand_total'] < 1000){ ?>
                    <h4>Dear Customer, your order total is LKR <?php echo $cart_total['grand_total']; ?>.00</h4>
                    <h4>Sorry to let you know, the minimum order value to check out is LKR 1500.00</h4>
                    <h4>Please do add products to proceed.</h4>
                    <button type="button" title="Continue Shopping" class="button btn-proceed-checkout" onclick="location.href = '/'" style="float:right; width: 35%;">
                        <span>CONTINUE SHOPPING</span>
                    </button>
                <?php }else{ ?>
                <ol class="one-page-checkout" id="checkoutSteps">
                    <li id="opc-billing" class="section allow active">
                        <div class="step-title">
                            <span class="number">1</span>
                            <h3 class="one_page_heading">Delivery Address</h3>
                        </div>
                        <div id="err_1" class="step a-item error-div"></div>
                        <div id="checkout-step-billing" class="step a-item">
                            <form id="co-billing-form" action="">
                                <fieldset class="group-select">
                                    <ul class="">
                                        <li class="wide"><label for="address_id">Select a
                                                delivery address from your address book or enter a new
                                                address.</label> <br>
                                            <div class="input-box">
                                                <select name="address_id"
                                                        id="address_id" class="address-select" title=""
                                                        >

                                                    <?php foreach ($address_book as $address): ?>
                                                        <option value="<?php echo $address['id']; ?>"
                                                        <?php
                                                        if ($get_checkout['delivery_address']['id'] == $address['id']) {
                                                            echo 'checked';
                                                        }
                                                        ?>><?php echo $address['address']; ?></option>
                                                            <?php endforeach; ?>

                                                </select>
                                            </div></li>
                                        <li id="billing-new-address-form" style="display: none;">
                                            <fieldset>

                                                <ul>

                                                    <li class="fields">
                                                    <div class="input-box">
                                                            <label for="street_number">Name<em
                                                                    class="required">*</em>
                                                            </label> <input type="text" id="guest_name"
                                                                            name="guest_name" value="" title="guest_name"
                                                                            class="input-text ">
                                                        </div>
                                                        
                                                        <div class="input-box">
                                                            <label for="street_number">Street Number<em
                                                                    class="required">*</em>
                                                            </label> <input type="text" id="street_number"
                                                                            name="street_number" value="" title="street_number"
                                                                            class="input-text ">
                                                        </div>
                                                        
                                                    </li>
                                                    <li class="wide"><label for="street_address">Street
                                                            Address<em class="required">*</em>
                                                        </label> <br> <input type="text" title="street_address"
                                                                             name="street_address" id="street_address"
                                                                             class="input-text  required-entry"></li>
                                                    <li class="fields">
                                                        <div class="input-box">
                                                            <label for="city">City<em class="required">*</em></label>
                                                            <input type="text" title="city" name="city"
                                                                   class="input-text  required-entry" id="city">
                                                        </div>

                                                        <div class="input-box">
                                                            <label for="country">Country<em
                                                                    class="required">*</em></label> <select
                                                                name="country" id="country"
                                                                class="validate-select" title="country">

                                                                <option value="LK" selected="selected">Sri Lanka</option>

                                                            </select>
                                                        </div>
                                                    </li>
                                                    <li class="fields">
                                                        <div class="input-box">
                                                            <label for="phone_number">Phone<em
                                                                    class="required">*</em></label> <input type="text"
                                                                                                   name="phone_number" value="" title="phone_number"
                                                                                                   class="input-text  required-entry"
                                                                                                   id="phone_number">
                                                        </div> 
                                                         <div class="input-box">
                                                            <label for="email_address">Email Address<em
                                                                    class="required">*</em>
                                                            </label> <input type="text" id="email_address"
                                                                            name="email_address" value="" title="email_address"
                                                                            class="input-text ">
                                                        </div>
                                                </ul>


                                                <script>
                                                    function showDiv()
                                                    {

                                                        if (document.getElementById('text1').style.display == "")
                                                        {
                                                            document.getElementById('text1').style.display = "none";
                                                            document.getElementById('text2').style.display = "none";

                                                        } else
                                                        {
                                                            document.getElementById('text1').style.display = "";
                                                        }




                                                    }
                                                </script>
                                            </fieldset>
                                        </li>

                                    </ul>
                                    <div class="buttons-set" id="billing-buttons-container">
                                        <p class="required">* Required Fields</p>
                                        <button type="button" title="Continue" class="button continue-guest"
                                                onClick="">
                                            <span>Continue</span>
                                        </button>
                                        <span class="please-wait" id="billing-please-wait"
                                              style="display: none;"> <?php echo $this->Html->image('opc-ajax-loader.gif', ['alt' => 'Loading next step...', 'title' => 'Loading next step...', 'class' => 'v-middle']); ?> Loading next step... </span>
                                    </div>
                                </fieldset>
                            </form>

                        </div>
                    </li>
                    <li id="opc-shipping" class="section">
                        <div class="step-title">
                            <span class="number">2</span>
                            <h3 class="one_page_heading">Delivery Date/Time</h3>
                        </div>
                        <div id="err_2" class="step a-item error-div"></div>
                        <div id="checkout-step-shipping" class="step a-item"
                             style="display: none;">
                            <form action="" id="co-shipping-form">
                                <ul class="">

                                    <li id="shipping-new-address-form" style="display: block;">
                                        <fieldset class="group-select">
                                            <ul>
                                                <li class="fields">
                                                    <div class="customer-name">
                                                        <div class="input-box name-firstname">
                                                            <label for="delivery_date">Date<span
                                                                    class="required">*</span></label>
                                                            <div class="input-box1">
                                                                <input type="text" id="datepicker" class="input-text required-entry delivery_date">
<!--                                                                <select name="delivery_date" value=""
                                                                        title="delivery_date"
                                                                        class="input-text required-entry delivery_date">
                                                                            <?php foreach ($get_checkout['next_days_for_delivery'] AS $next_day) { ?>
                                                                        <option value="<?php echo $next_day; ?>"><?php echo $next_day; ?></option>
                                                                    <?php } ?>
                                                                </select>-->
                                                            </div>
                                                        </div>
                                                        <div class="input-box name-lastname">
                                                            <label for="delivery_time">Time<span class="required">*</span></label>
                                                            <div>
                                                                <select id="delivery_time" name="delivery_time" value="" title="delivery_time" class="input-text delivery_time">
                                                                    
                                                                </select>
                                                                <div id="delivery_message"></div>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </fieldset>
                                    </li>

                                </ul>
                                <div class="buttons-set" id="shipping-buttons-container">
                                    <p class="required">* Required Fields</p>
                                    <button type="button" class="button continue-guest" title="Continue"
                                            onClick="">
                                        <span>Continue</span>
                                    </button>
                                    <a href="#" onClick="return false;"
                                       class="back"><small>« </small>Back</a> <span
                                       id="shipping-please-wait" class="please-wait"
                                       style="display: none;"> <?php echo $this->Html->image('opc-ajax-loader.gif', ['alt' => 'Loading next step...', 'title' => 'Loading next step...', 'class' => 'v-middle']); ?> Loading next step... </span>
                                </div>
                            </form>

                        </div>
                    </li>


                    <li id="opc-review" class="section">
                        <div class="step-title">
                            <span class="number">3</span>
                            <h3 class="one_page_heading">Complete Checkout</h3>
                        </div>
                        <div id="err_3" class="step a-item error-div"></div>
                        <div id="checkout-step-review" class="step a-item"
                             style="display: none;">
                            <div class="order-review" id="checkout-review-load">
                                <!-- Content loaded dynamically -->
                                <div class="buttons-set" id="shipping-buttons-container">
									<input type="radio" name="payment_method" value="1" checked> Cash<!-- cash --><br>
									<input type="radio" name="payment_method" value="2"> Card<!-- card --><br>
									<input type="radio" name="payment_method" value="3"> Online Paymet<!-- online --><br>
									<!-- Thank you for your order, we will mail you a payment link once the order is processed -->
									<br>
									<div class="" id="succsess_3"></div>
                                    <button type="button" class="button continue-guest" title="Continue"
                                            onClick="">
                                        <span>Complete Checkout</span>
                                    </button>
                                    <a href="#" onClick="return false;"
                                       class="back"><small>« </small>Back</a> <span
                                       id="review-please-wait" class="please-wait"
                                       style="display: none;"> <?php echo $this->Html->image('opc-ajax-loader.gif', ['alt' => 'Loading next step...', 'title' => 'Loading next step...', 'class' => 'v-middle']); ?> Loading next step... </span>
                                <button type="button" class="button" id="go-to-dashboard" title="Continue"
                                            onClick="location.href = '/user/dashboard'" style="display:none;">
                                        <span>Go to Dashboard</span>
                                    </button>
								
								</div>

                            </div>
                        </div>
                    </li>

                </ol>
                <?php } ?>
                <br>
            </section>
            <aside
                class="col-right sidebar col-sm-3 wow bounceInUp animated animated"
                style="visibility: visible;">
                <div id="checkout-progress-wrapper">
                    <div class="block block-progress">
                        <div class="block-title">Your Checkout</div>
                        <div class="block-content">
                            <dl>
                                <div id="billing-progress-opcheckout">
                                    <dt>Billing Address</dt>
                                </div>
                                <div id="shipping-progress-opcheckout">
                                    <dt>Shipping Address</dt>
                                </div>
                                <div id="shipping_method-progress-opcheckout">
                                    <dt>Shipping Method</dt>
                                </div>
                                <div id="payment-progress-opcheckout">
                                    <dt>Payment Method</dt>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </aside>
            <!--col-right sidebar-->
        </div>
        <!--row-->
    </div>
    <!--main-container-inner-->
</div>
<!--main-container col2-left-layout-->

<!-- For version 1,2,3,4,6 -->

<?php
//http://stackoverflow.com/questions/8761713/jquery-ajax-loading-image
?>
  