
<div class="page-heading">
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <ul>
                        <li class="home"> <a href="/" title="Go to Home Page">Home</a> <span>—› </span> </li>
<!--                        <li class="category1599"> <a href="grid.html" title="">Salad</a> <span>—› </span> </li>
                        <li class="category1600"> <a href="grid.html" title="">Bread Salad</a> <span>—› </span> </li>-->
                        <li class="category1601"> <strong><?php echo $category->title; ?></strong> </li>
                    </ul>
                </div>
                <!--col-xs-12--> 
            </div>
            <!--row--> 
        </div>
        <!--container--> 
    </div>
    <div class="page-title">
        <h2><?php echo $category->title; ?></h2>
    </div>
</div>
<!--breadcrumbs--> 
<!-- BEGIN Main Container col2-left -->
<section class="main-container col2-left-layout bounceInUp animated"> 
    <!-- For version 1, 2, 3, 8 --> 
    <!-- For version 1, 2, 3 -->
    <div class="container">
        <div class="row">
            <div class="col-main col-sm-9 col-sm-push-3 product-grid">
                <div class="pro-coloumn">
                    <article class="col-main">
                        <div class="toolbar">
                            <div class="sorter" style="visibility: hidden;">
                                <div class="view-mode"> <span title="Grid" class="button button-active button-grid">&nbsp;</span><a href="list.html" title="List" class="button-list">&nbsp;</a> </div>
                            </div>
                            <div id="sort-by" style="display: none;">
                                <label class="left">Sort By: </label>
                                <ul>
                                    <li><a href="#">Position<span class="right-arrow"></span></a>
                                        <ul>
                                            <li><a href="#">Name</a></li>
                                            <li><a href="#">Price</a></li>
                                            <li><a href="#">Position</a></li>
                                        </ul>
                                    </li>
                                </ul>
                                <a class="button-asc left" href="#" title="Set Descending Direction"><span class="top_arrow"></span></a> </div>
                            <div class="pager">
                                <div id="limiter" style="display:none;">
                                    <label>View: </label>
                                    
                                    <ul>
                                        <li><a href="#">15<span class="right-arrow"></span></a>
                                            <ul>
                                                <li><a href="#">20</a></li>
                                                <li><a href="#">30</a></li>
                                                <li><a href="#">35</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                <div class="pages">
                                    <label>Page:</label>
                                    <ul class="pagination">
                                        <?= $this->Paginator->options(['url' => $paginateUrl])?>
                                        <?= $this->Paginator->prev('< ' . __('previous')) ?>
                                        <?= $this->Paginator->numbers() ?>
                                        <?= $this->Paginator->next(__('next') . ' >') ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="category-products">
                            <ul class="products-grid">
                                <?php
                                foreach ($products AS $product) {
                                   
                                    ?>
                                    <li class="item col-lg-4 col-md-3 col-sm-4 col-xs-6">
                                        <div class="item-inner">
                                            <div class="item-img">
                                                <div class="item-img-info"><a href="/product/<?php echo $product->slug; ?>" title="Retis lapen casen" class="product-image"><img src="/products-images/product-img.jpg" alt="Retis lapen casen"></a>

                                                    <div class="item-box-hover">
                                                        <div class="box-inner">
                                                            <div class="product-detail-bnt"><a class="button detail-bnt" type="button"><span>Quick View</span></a></div>
                                                            <div class="actions"><span class="add-to-links"><a href="#" class="link-wishlist" title="Add to Wishlist"><span>Add to Wishlist</span></a> <a href="#" class="link-compare add_to_compare" title="Add to Compare"><span>Add to Compare</span></a></span> </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="item-info">
                                                <div class="info-inner">
                                                    <div class="item-title"><a href="/product/<?php echo $product->slug; ?>" title="Retis lapen casen"><?php echo $product->name; ?></a> </div>
                                                    <div class="item-content">
                                                        <div class="rating">
                                                            <div class="ratings">
                                                                <div class="rating-box">
                                                                    <div class="rating" style="width:80%"></div>
                                                                </div>
                                                                <!--<p class="rating-links"><a href="#">0 Review(s)</a> <span class="separator">|</span> <a href="#">Add Review</a> </p>-->
                                                            </div>
                                                        </div>
                                                        <div class="item-price">
                                                            <div class="price-box"><span class="regular-price" id="product-price-1"><span class="price">LKR<?php echo ' '.$product->price; ?></span> </span> </div>
                                                        </div>
                                                        <div class="add_cart">
                                                            <button class="button btn-cart" type="button"><span>Add to Cart</span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </li>
<?php } ?>


                            </ul>
                        </div>
                    </article>
                </div>
                <!--	///*///======    End article  ========= //*/// --> 
            </div>
            
            <?php include 'category-sidebar.ctp'; ?>
            <!--col-right sidebar--> 
        </div>
        <!--row--> 
    </div>
    <!--container--> 
</section>
<!--main-container col2-left-layout--> 

<div class="our-features-box wow bounceInUp animated animated">
    <div class="container">
        <ul>
            <li>
                <div class="feature-box free-shipping">
                    <div class="icon-truck"></div>
                    <div class="content">FREE SHIPPING on order over LKR 99</div>
                </div>
            </li>
            <li>
                <div class="feature-box need-help">
                    <div class="icon-support"></div>
                    <div class="content">Need Help +1 800 123 1234</div>
                </div>
            </li>
            <li>
                <div class="feature-box money-back">
                    <div class="icon-money"></div>
                    <div class="content">Money Back Guarantee</div>
                </div>
            </li>
            <li class="last">
                <div class="feature-box return-policy">
                    <div class="icon-return"></div>
                    <div class="content">30 days return Service</div>
                </div>
            </li>
        </ul>
    </div>
</div>