<div class="content">
    <div id="thm-mart-slideshow" class="thm-mart-slideshow">
        <div class="container">
            <div id='thm_slider_wrapper' class='thm_slider_wrapper fullwidthbanner-container' >
                <div id='thm-rev-slider' class='rev_slider fullwidthabanner'>
                    <ul>
                        <li data-transition='random' data-slotamount='7' data-masterspeed='1000' data-thumb='/slider-images/union-bank-debit-card-web-banner.png'>
                            <img src='/slider-images/union-bank-debit-card-web-banner.png'  data-bgposition='center center'  data-bgfit='contain' data-bgrepeat='no-repeat' alt="slider-image1" />
                        </li>
						
						<li data-transition='random' data-slotamount='7' data-masterspeed='1000' data-thumb='/slider-images/dfcc-new.jpg'>
                            <img src='/slider-images/dfcc.png'  data-bgposition='center center'  data-bgfit='contain' data-bgrepeat='no-repeat' alt="slider-image2" />
                        </li>

                        <li data-transition='random' data-slotamount='7' data-masterspeed='1000' data-thumb='/slider-images/free_delivery.jpg'>
                            <img src='/slider-images/free_delivery.jpg'  data-bgposition='center center'  data-bgfit='contain' data-bgrepeat='no-repeat' alt="slider-image1" />
                        </li>

                        <li data-transition='random' data-slotamount='7' data-masterspeed='1000' data-thumb='/slider-images/GROCERY.jpg'>
                            <img src='/slider-images/GROCERY.jpg'  data-bgposition='center center'  data-bgfit='contain' data-bgrepeat='no-repeat' alt="slider-image1" />
                        </li>

                        <li data-transition='random' data-slotamount='7' data-masterspeed='1000' data-thumb='/slider-images/io.jpg'>
                            <img src='/slider-images/io.jpg'  data-bgposition='center center'  data-bgfit='contain' data-bgrepeat='no-repeat' alt="slider-image1" />
                        </li>
                        
                        <li data-transition='random' data-slotamount='7' data-masterspeed='1000' data-thumb='/slider-images/to.jpg'>
                            <img src='/slider-images/to.jpg'  data-bgposition='center center'  data-bgfit='contain' data-bgrepeat='no-repeat' alt="slider-image1" />
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    

    <div class="our-features-box wow bounceInUp animated animated" style="margin-top:-100px !important;">
        <div class="container">
            <ul>
                <li>
                    <div class="feature-box free-shipping">
                        <div class="icon-truck"></div>
                        <div class="content">FREE SHIPPING <br/> IN COLOMBO</div>
                    </div>
                </li>
                <li>
                    <div class="feature-box need-help">
                        <div class="icon-support"></div>
                        <div class="content">ORDER VIA MOBILE <br/> 011 7 55 66 00</div>
                    </div>
                </li>
                <li>
                    <div class="feature-box money-back">
                        <div class="icon-money"></div>
                        <div class="content">CHEAPEST PRICE</div>
                    </div>
                </li>
                <li class="last">
                    <div class="feature-box return-policy">
                        <div class="icon-return"></div>
                        <div class="content">100% FRESH PRODUCTS</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!--Category slider Start-->
    <div class="top-cate" style="margin-top: -80px;display:none;">
        <div class="featured-pro container">
            <div class="row">
                <div class="slider-items-products">
                    <div class="new_title">
                        <h2>Top Categories</h2>

                    </div> 
                    <div id="top-categories" class="product-flexslider hidden-buttons">
                        <div class="slider-items slider-width-col4 products-grid">
                            <?php foreach ($category_tree[0] AS $main_cat) { ?>
                                <div class="item">
                                    <a href="/products/category/<?php echo $main_cat['slug']; ?>">
                                        <div class="pro-img"><img src="/category-images/<?php echo $main_cat['image']; ?>" alt="<?php echo $main_cat['title']; ?>">
                                            <div class="pro-info"><?php echo $main_cat['title']; ?></div>
                                        </div>
                                    </a>
                                </div>
                                <!-- Item -->
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Category silder End--> 

    <!--    <div id="top">
            <div class="container">
                <div class="row">
                    <ul>
                        <li>
                            <div>
                                <a href="#" data-scroll-goto="1">
                                    <img src="/images/banner.png" alt="promotion-banner1">
                                </a>
                            </div>
                        </li>
                        <li>
                            <div>
                                <a href="#" data-scroll-goto="2">
                                    <img src="/images/banner.png" alt="promotion-banner2">
                                </a>
                            </div>
                        </li>
                        <li>
                            <div>
                                <a href="#" data-scroll-goto="3">
                                    <img src="/images/banner.png" alt="promotion-banner3">
                                </a>
                            </div>
                        </li>
    
                    </ul>
                </div>
            </div>
        </div>-->






    <!-- best Pro Slider -->
    <section class=" wow bounceInUp animated" >
        <div class="best-pro slider-items-products container">
            <div class="new_title">
                <h2>Latest Products</h2>
            </div>
            <!--<div class="cate-banner-img"><img src="/images/bestseller-banner.png" alt="Retis lapen casen"></div>-->
            <div id="best-seller" class="product-flexslider hidden-buttons">
                <div class="slider-items slider-width-col4 products-grid">
                    <?php foreach ($latest_products AS $latest_product) { ?>
                        <div class="item">
                            <div class="item-inner">
                                <div class="item-img">
                                    <div class="item-img-info"><a href="/product/<?php echo $latest_product['slug']; ?>" title="<?php echo $latest_product['name']; ?>" class="product-image">
                                            <img src="<?php echo $latest_product['image']; ?>" alt="<?php echo $latest_product['name']; ?>"></a>
                                        <div class="new-label new-top-left">New</div>
                                        <div class="item-box-hover">
                                            <div class="box-inner">
                                                <div class="product-detail-bnt"><a class="button detail-bnt"><span>Quick View</span></a></div>
                                                <div class="actions"><span class="add-to-links"><a href="#" class="link-wishlist" title="Add to Wishlist"><span>Add to Wishlist</span></a> <a href="#" class="link-compare add_to_compare" title="Add to Compare"><span>Add to Compare</span></a></span> </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item-info"> 
                                    <div class="info-inner">
                                        <div class="item-title"><a href="/product/<?php echo $latest_product['slug']; ?>" title="<?php echo $latest_product['name']; ?>"><?php echo $latest_product['name']; ?></a> </div>
                                        <div class="item-content">
                                            <div class="rating">
                                                <div class="ratings">
                                                    <div class="rating-box">
                                                        <div class="rating" style="width:80%"></div>
                                                    </div>
                                                    <!--<p class="rating-links"><a href="#">1 Review(s)</a> <span class="separator">|</span> <a href="#">Add Review</a> </p>-->
                                                </div>
                                            </div>
                                            <div class="item-price">
                                                <div class="price-box"><span class="regular-price" ><span class="price">LKR<?php echo ' ' . $latest_product['price']; ?>.00</span> </span> </div>
                                            </div>
                                            <div class="add_cart">
                                                <?php /* ?>
                                                  <input name="qty" id="qty" maxlength="12" value="1" title="Quantity" class="input-text qty" type="hidden" class="add_to_cart_product_qty">
                                                  <input name="product_id" id="product_id" value="<?php echo $latest_product->id?>" type="hidden" class="add_to_cart_product_id">
                                                  <?php */ ?>
                                                <div class="custom pull-left add-to-cart add-to-cart-grid">
                                                    <button onClick="var result = document.getElementById('<?php echo 'qty-' . $latest_product->id; ?>'); var qty = result.value; if (!isNaN(qty) && qty > 0)
                                                                result.value--;
                                                            return false;" class="reduced items-count" type="button"><i class="icon-minus">&nbsp;</i></button>

                                                    <input type="text" name="qty" id="<?php echo 'qty-' . $latest_product->id; ?>" size="1" maxlength="12" value="1" title="Quantity:" class="input-text qty add_to_cart_product_qty">
                                                    <input type="hidden" name="product_id" id="product_id" value="<?php echo $latest_product->id; ?>" class="add_to_cart_product_qty">
                                                    <button onClick="var result = document.getElementById('<?php echo 'qty-' . $latest_product->id; ?>');
                                                            var qty = result.value; if (!isNaN(qty))
                                                                result.value++;return false;" class="increase items-count" type="button"><i class="icon-plus">&nbsp;</i></button>
													<span>
                      						<i class="fa fa-spinner fa-spin fa-2x fa-fw margin-bottom single-product-add-loading single-product-add-loading-grid product-add-loading" style="display: none;"></i>
                      						<i class="fa fa-check fa-2x fa-fw margin-bottom single-product-add-loading single-product-add-loading-grid product-add-success" style="display: none;"></i>
                      						<i class="fa fa-times fa-2x fa-fw margin-bottom single-product-add-loading single-product-add-loading-grid product-add-err" style="display: none;"></i>
                      					</span>
                                                </div>    
                                                <button class="button btn-cart add-to-cart-jq-function" type="button"><span>Add to Cart</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<?php } ?>
                </div>
            </div>
        </div>
    </section>

</div>