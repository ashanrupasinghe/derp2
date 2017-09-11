
<div class="page-heading">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-title">
					<h2>Login or Create an Account</h2>
				</div>
			</div>
			<!--col-xs-12-->
		</div>
		<!--row-->
	</div>
	<!--container-->
</div>


<!-- BEGIN Main Container -->

<div class="main-container col1-layout wow bounceInUp animated animated"
	style="visibility: visible;">

	<div class="main">
		<div class="account-login container">
			<!--page-title-->

			<!-- <form action="" method="post" id="login-form"> -->
        <?= $this->Form->create(null,['id'=>'user-register-form-x'])?>        
        
			<fieldset class="col2-set">
				<div class="col-1 new-users">
					<strong>Registered Customers</strong>
					<div class="content">

						<p>If you have an account with us, please log in.</p>
						
						<div class="buttons-set">
							<button type="button" title="Login to Account"
								class="button create-account" onClick="location.href = '<?php echo $this->Url->build($login_url);?>'">
								<span><span>Login To The Account</span></span>
							</button>
							
							<button type="button" title="Checkout as a Guest"
								class="button guest-checkout-btn" onClick="location.href = '<?php echo $this->Url->build('/order/guest-checkout');?>'">
								<span><span>Checkout as a Guest User</span></span>
							</button>
						</div>
					</div>
				</div>
				<div class="col-2 registered-users">
					<strong>New Customers</strong>
					<div class="content">

						<p>By creating an account with our store, you will be able to move
							through the checkout process faster, store multiple shipping
							addresses, view and track your orders in your account and more.</p>
							<?= $this->Flash->render() ?>
						<ul class="form-list">
							<li><label for="firstName">First Name<em class="required">*</em></label>
								<div class="input-box">
									<input type="text" name="firstName" value="" id="firstName"
										class="input-text required-entry validate-email"
										title="First Name" required>
								</div></li>
							<li><label for="lastName">Last Name<em class="required">*</em></label>
								<div class="input-box">
									<input type="text" name="lastName" value="" id="lastName"
										class="input-text required-entry validate-email"
										title="Last Name" required>
								</div></li>
							<li><label for="username">Email Address<em class="required">*</em></label>
								<div class="input-box">
									<input type="email" name="email" value="" id="email"
										class="input-text required-entry validate-email"
										title="Email Address" required>
								</div></li>
								<li><label for="mobileNo">Mobile Number<em class="required">*</em></label>
								<div class="input-box">
									<input type="text" name="mobileNo" value="" id="mobileNo"
										class="input-text required-entry"
										title="Mobile Number" required>
								</div></li>
								<li><label for="password">Password<em class="required">*</em></label>
								<div class="input-box">
									<input type="password" name="password"
										class="input-text required-entry validate-password"
										id="password" title="Password" required>
								</div></li>
								<li><label for="confirm_password">Confirm Password<em class="required">*</em></label>
								<div class="input-box">
									<input type="password" name="confirm_password"
										class="input-text required-entry validate-password"
										id="confirm_password" title="Confirm Password" required>
								</div></li>
								
								<li>
								<input type="hidden" name="newsLetter" value="0" />
			 					<input type="checkbox" name="newsLetter" value="1" /> &nbsp;signup for newsletter
								</li>
						</ul>
						<input type="hidden" name="login_type"
										class="input-text required-entry"
										id="login_type"  value="0">
						<input type="hidden" name="formType"
										class="input-text required-entry"
										id="formType"  value="login-customer">			
										
						<div class="remember-me-popup">
							<div class="remember-me-popup-head" style="display: none">
								<h3 id="text2">What's this?</h3>
								<a href="#" class="remember-me-popup-close" onClick="showDiv()"
									title="Close">Close</a>
							</div>
							<div class="remember-me-popup-body" style="display: none">
								<p id="text1">Checking "Remember Me" will let you access your
									shopping cart on this computer when you are logged out</p>
								<div class="remember-me-popup-close-button a-right">
									<a href="#" class="remember-me-popup-close button"
										title="Close" onClick="
            showDiv()"><span>Close</span></a>
								</div>
							</div>
						</div>

						<p class="required">* Required Fields</p>



						<div class="buttons-set">

							<button type="submit" class="button login user-login-btn-send2" title="Register"
								 id="send2">
								<span>Register</span>
							</button>


						</div>
						<!--buttons-set-->
					</div>
					<!--content-->
				</div>
				<!--col-2 registered-users-->
			</fieldset>
			<!--col2-set-->
    <?= $this->Form->end()?>
   
</div>
		<!--account-login-->

	</div>
	<!--main-container-->

</div>
<!--col1-layout-->




<div class="our-features-box wow bounceInUp animated animated">
	<div class="container">
		<ul>
			<li>
				<div class="feature-box free-shipping">
					<div class="icon-truck"></div>
					<div class="content">FREE SHIPPING on order over LKR99</div>
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
<!-- For version 1,2,3,4,6 -->

