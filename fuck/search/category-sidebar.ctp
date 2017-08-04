<?php
//echo '<pre>';
//print_r($sub_categories['1']);
?>
<aside class="col-left sidebar col-sm-3 col-xs-12 col-sm-pull-9 wow bounceInUp animated"> 
    <!-- BEGIN SIDE-NAV-CATEGORY -->
    <div class="side-nav-categories">
        <div class="block-title"> Categories </div>
        <!--block-title--> 
        <!-- BEGIN BOX-CATEGORY -->
        <div class="box-content box-category">
            <ul>
                <?php
                $i = 0;
				$slug = $cat_slug;
				$parents = $this->Plugins->getParents($category->id,$category->level);
				
				if($category->level == 0){
					$parent_id = -1;
					$top_parent_id = $category->id;
				}else if($category->level == 1){
					$parent_id = -1;
					$top_parent_id = $parents['parent_id'];
				}else{
					$parent_id = $parents['parent_id'];
					$top_parent_id = $parents['top_parent_id'];
				}
				
				//print_r($parents);
				//echo $category->level;
                foreach ($category_tree[0] AS $main_cat) {
					
					//echo $main_cat->id; 
                    ?>
                    <li > <a 
					class="<?php echo ($main_cat['id'] == $top_parent_id ? 'active' : '') ?>" 
					href="/products/category/<?php echo $main_cat['slug']; ?>">
					<?php echo $main_cat['title']; ?></a>
					
					<span class="subDropdown <?php echo ($main_cat['id'] == $top_parent_id ? 'minus' : 'plus'); ?>"></span>

                        <ul class="level0_<?php echo $main_cat['id']; ?>" style="display:<?php echo ($main_cat['id'] == $top_parent_id ? 'block' : 'none'); ?>">
                            <?php foreach ($category_tree[1][$main_cat['id']] AS $sub_cat) { ?>
                                <li> <a href="/products/category/<?php echo $sub_cat['slug']; ?>"> <?php echo $sub_cat['title']; ?> </a>
								<span class="subDropdown <?php echo (($parent_id > 0 && $sub_cat['id'] == $parent_id) ? 'minus': 'plus' ); ?>"></span>
                                    <ul class="level1" style="<?php echo (($parent_id > 0 && $sub_cat['id'] == $parent_id) ? 'display:block;': 'display:none;' ); ?>">
                                        <?php foreach ($category_tree[2][$sub_cat['id']] AS $sub_sub_cat) { ?>
                                            <li> <a href="/products/category/<?php echo $sub_sub_cat['slug']; ?>"> <?php echo $sub_sub_cat['title']; ?> </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                    $i++;
                }
                ?>
            </ul>
        </div>
        <!--box-content box-category--> 
    </div>
    <!--side-nav-categories-->
    <div class="block block-layered-nav">
        <div class="block-title"> Shop By </div>
        <div class="block-content">
            <p class="block-subtitle">Shopping Options</p>
            <dl id="narrow-by-list">
                <dt class="odd">Category</dt>
                <dd class="odd">
                    <ol>
                       <?php foreach ($category_tree[0] AS $main_cat) { ?>
                            <li> <a href="/products/category/<?php echo $main_cat['slug']; ?>"> <?php echo $main_cat['title']; ?> </a> </li>
                        <?php } ?>
                    </ol>
                </dd>
                <!--                            <dt class="last odd">Price</dt>
                                            <dd class="last odd">
                                                <ol>
                                                    <li> <a href="#"> <span class="price">LKR0.00</span> - <span class="price">LKR99.99</span> <span class="count">(26)</span> </a> </li>
                                                    <li> <a href="#"> <span class="price">LKR100.00</span> and above <span class="count">(3)</span> </a> </li>
                                                </ol>
                                            </dd>-->
            </dl>
        </div>
    </div>
    <div class="custom-slider" style="display: none;">
        <div>
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li class="active" data-target="#carousel-example-generic" data-slide-to="0"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1" class=""></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2" class=""></li>
                </ol>
                <div class="carousel-inner">
                    <div class="item active"><img src="/images/blog-banner.png" alt="slide3">
                        <div class="carousel-caption">
                            <h3><a title=" Sample Product" href="product-detail.html">50% OFF</a></h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            <a class="link" href="#">Buy Now</a></div>
                    </div>
                    <div class="item"><img src="/images/blog-banner.png" alt="slide1">
                        <div class="carousel-caption">
                            <h3><a title=" Sample Product" href="product-detail.html">Hot collection</a></h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                    <div class="item"><img src="/images/blog-banner.png" alt="slide2">
                        <div class="carousel-caption">
                            <h3><a title=" Sample Product" href="product-detail.html">Summer collection</a></h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                </div>
                <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev"> <span class="sr-only">Previous</span> </a> <a class="right carousel-control" href="#carousel-example-generic" data-slide="next"> <span class="sr-only">Next</span> </a></div>
        </div>
    </div>

    <div class="block block-list block-cart" style="display: none;">
        <div class="block-title"> My Cart </div>
        <div class="block-content">
            <?php if (sizeof($cart_products) > 0): ?>
            <div class="summary">
                <p class="amount">There are <a href="#"><?php echo $cart_size; ?> items</a> in your cart.</p>
                <p class="subtotal"> <span class="label">Cart Subtotal:</span> <span class="price">LKR<?php echo ' '.$total['grand_total']; ?>.00</span> </p>
            </div>
            <div class="ajax-checkout">
                <button type="button" title="Checkout" class="button button-checkout" onClick="#"> <span>Checkout</span> </button>
            </div>
            <p class="block-subtitle">Recently added item(s)</p>
            <ul id="cart-sidebar" class="mini-products-list">
                 <?php foreach ($cart_products as $product): ?>
                <li class="item">
                    <div class="item-inner"> <a href="#" class="product-image"><img src="products-images/product-img.jpg" width="80" alt="product"></a>
                        <div class="product-details">
                            <div class="access"> <a href="#" class="btn-remove1">Remove</a> 
                                <a href="" title="Edit item" class="btn-edit">
                                    <i class="icon-pencil"></i><span class="hidden">Edit item</span></a> </div>
                            <!--access--> 

                            <strong>1</strong> x <span class="price">LKR 299.00</span>
                            <p class="product-name"><a href="#">RETIS LAPEN CASEN</a></p>
                        </div>
                        <!--product-details-bottoms--> 
                    </div>
                </li>
                <?php endforeach; ?>
                <li class="item  last1">
                    <div class="item-inner"> <a href="#" class="product-image"><img src="products-images/product-img.jpg" width="80" alt="product"></a>
                        <div class="product-details">
                            <div class="access"> <a href="#" class="btn-remove1">Remove</a> 
                                <a href="" title="Edit item" class="btn-edit">
                                    <i class="icon-pencil"></i><span class="hidden">Edit item</span></a> </div>
                            <!--access--> 

                            <strong>1</strong> x <span class="price">LKR 299.00</span>
                            <p class="product-name"><a href="#">RETIS LAPEN CASEN</a></p>
                        </div>
                        <!--product-details-bottoms--> 
                    </div>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>

    <!--block block-list block-compare--> 

    <!--<div class="hot-banner"><img src="/images/hot-trends-banner.png" alt="banner"></div>-->  
</aside>
