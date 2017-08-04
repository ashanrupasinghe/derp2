<div
	style="margin: 0; padding: 0; color: #333333; font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-style: normal; font-weight: normal; line-height: 1.42857143; font-size: 14px; text-align: left; background-color: #f5f5f5">



	<span class="HOEnZb"><font color="#888888"> </font></span>
	<table class="m_7924699770022590990m_-1635589391856875088wrapper"
		style="border-collapse: collapse; margin: 0 auto" width="100%">
		<tbody>
			<tr>
				<td class="m_7924699770022590990m_-1635589391856875088wrapper-inner"
					style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding-bottom: 30px; width: 100%"
					align="center"><span class="HOEnZb"><font color="#888888"> </font></span><span
					class="HOEnZb"><font color="#888888"> </font></span>
					<table class="m_7924699770022590990m_-1635589391856875088main"
						style="border-collapse: collapse; margin: 0 auto; text-align: left; width: 660px"
						align="center">
						<tbody>
							<tr>
								<td class="m_7924699770022590990m_-1635589391856875088header"
									style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 25px; background-color: #f5f5f5">
									<a class="m_7924699770022590990m_-1635589391856875088logo"
									href="http://direct2door.lk/"
									style="color: #1979c3; text-decoration: none" target="_blank"
									data-saferedirecturl="https://www.google.com/url?hl=en-GB&amp;q=http://direct2door.lk/&amp;source=gmail&amp;ust=1495423142493000&amp;usg=AFQjCNHYnWA3Tx9k7okPO6Rt2Yw2UjKWjA">
										<img
										src="<?php echo $this->Url->build('/', true).'img/logo_email.png';?>"
										alt="Direct 2 door"
										style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none"
										class="CToWUd" width="250" border="0" height="52">
								</a>
								</td>
							</tr>
							<tr>
								<td
									class="m_7924699770022590990m_-1635589391856875088main-content"
									style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; background-color: #ffffff; padding: 25px">


									<table style="border-collapse: collapse">
										<tbody>
											<tr
												class="m_7924699770022590990m_-1635589391856875088email-intro">
												<td
													style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding-bottom: 20px">
													<p
														class="m_7924699770022590990m_-1635589391856875088greeting"
														style="margin-top: 0; margin-bottom: 10px"><?= $order->customer->firstName.' '.$order->customer->lastName?>,</p>
													<span>
														<p style="margin-top: 0; margin-bottom: 10px">Thank you
															for your order from Direct 2 door. Once your package
															ships we will send you a tracking number.</p>
														<p style="margin-top: 0; margin-bottom: 10px">
															If you have questions about your order, you can email us
															at <a href="mailto:info@direct2door.lk"
																style="color: #1979c3; text-decoration: none"
																target="_blank">info@direct2door.lk</a>.
														</p>
												</span>
												</td>
											</tr>
											<tr
												class="m_7924699770022590990m_-1635589391856875088email-summary">
												<td
													style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top">
													<h1
														style="font-weight: 300; line-height: 1.1; font-size: 26px; margin-top: 0; margin-bottom: 10px; border-bottom: 1px solid #dbdbdb; padding-bottom: 10px">
														Your Order <span
															class="m_7924699770022590990m_-1635589391856875088no-link"><?= __('Order '.'#'.$order->id) ?></span>
													</h1>
													<p style="margin-top: 0; margin-bottom: 10px">
														Placed on <span
															class="m_7924699770022590990m_-1635589391856875088no-link"><?= $order->created ?></span>
													</p>
													<?php 
													//$delivery_date_time=$order->shipping[0]->delivery_date_time;
													?>
													<p style="margin-top: 0; margin-bottom: 10px">
														Delivery date <span
															class="m_7924699770022590990m_-1635589391856875088no-link"><?= $order->deliveryDate ?></span>
															
													</p>
													<p style="margin-top: 0; margin-bottom: 10px">														
															Delivery Time <span
															class="m_7924699770022590990m_-1635589391856875088no-link"><?= $order->deliveryTime ?></span>
													</p>
												</td>
											</tr>
											<tr
												class="m_7924699770022590990m_-1635589391856875088email-information">
												<td
													style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top">

													<table
														class="m_7924699770022590990m_-1635589391856875088order-details"
														style="border-collapse: collapse; width: 100%">
														<tbody>
															<tr>
																<td
																	class="m_7924699770022590990m_-1635589391856875088address-details"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px 10px 10px 0; width: 50%">
																	<h3
																		style="font-weight: 300; line-height: 1.1; font-size: 18px; margin-top: 0; margin-bottom: 10px">Billing
																		Info</h3>
																	<p style="margin-top: 0; margin-bottom: 10px"><?= $order->customer->firstName.' '.$order->customer->lastName?><br>

<?= $order->address ?> <br>
<br> Sri Lanka<br> T: <a
																			href="tel:<?= $order->customer->mobileNo ?>"
																			value="<?= $order->customer->mobileNo ?>"
																			target="_blank"><?= $order->customer->mobileNo ?></a>
																	</p>
																</td>

																<td
																	class="m_7924699770022590990m_-1635589391856875088address-details"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px 10px 10px 0; width: 50%">
																	<h3
																		style="font-weight: 300; line-height: 1.1; font-size: 18px; margin-top: 0; margin-bottom: 10px">Shipping
																		Info</h3>
																	<p style="margin-top: 0; margin-bottom: 10px"><?= $order->customer->firstName.' '.$order->customer->lastName?><br>

<?= $order->address ?> <br>



<br> Sri Lanka<br> T: <a
																			href="tel:<?= $order->customer->mobileNo ?>"
																			value="<?= $order->customer->mobileNo ?>"
																			target="_blank"><?= $order->customer->mobileNo ?></a>
																	</p>
																</td>

															</tr>
															<tr>
																<td
																	class="m_7924699770022590990m_-1635589391856875088method-info"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px 10px 10px 0; width: 50%">
																	<h3
																		style="font-weight: 300; line-height: 1.1; font-size: 18px; margin-top: 0; margin-bottom: 10px">Payment
																		Method</h3>
																	<dl
																		class="m_7924699770022590990m_-1635589391856875088payment-method"
																		style="margin-bottom: 10px; margin-top: 0">
																		<dt
																			class="m_7924699770022590990m_-1635589391856875088title"
																			style="font-weight: 400; margin-bottom: 5px; margin-top: 0">Cash
																			On Delivery</dt>
																	</dl>
																</td>

																<td
																	class="m_7924699770022590990m_-1635589391856875088method-info"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px 10px 10px 0; width: 50%">
																	<h3
																		style="font-weight: 300; line-height: 1.1; font-size: 18px; margin-top: 0; margin-bottom: 10px">Shipping
																		Method</h3>
																	<p style="margin-top: 0; margin-bottom: 10px">Free
																		Shipping - Free</p>

																</td>

															</tr>
														</tbody>
													</table>
													<table
														class="m_7924699770022590990m_-1635589391856875088email-items"
														style="border-collapse: collapse; width: 100%; border-spacing: 0; max-width: 100%">
														<thead>
															<tr>
																<th
																	class="m_7924699770022590990m_-1635589391856875088item-info"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; text-align: left; vertical-align: bottom; padding: 10px">
																	Items</th>
																<th
																	class="m_7924699770022590990m_-1635589391856875088item-qty"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; text-align: center; vertical-align: bottom; padding: 10px">
																	Qty</th>
																<th
																	class="m_7924699770022590990m_-1635589391856875088item-price"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; text-align: right; vertical-align: bottom; padding: 10px">
																	Price</th>
															</tr>
														</thead>
														<?php foreach($order->order_products as $product): ?>  
														
														<tbody>
															<tr>
																<td
																	class="m_7924699770022590990m_-1635589391856875088item-info"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px; border-top: 1px solid #dbdbdb">
																	<p
																		class="m_7924699770022590990m_-1635589391856875088product-name"
																		style="margin-top: 0; margin-bottom: 5px; font-weight: 700"><?php echo $product['product']->name;?>
																		</p>
																	<p
																		class="m_7924699770022590990m_-1635589391856875088sku"
																		style="margin-top: 0; margin-bottom: 0">SKU: <?php echo $product['product']->sku; ?>
																		Atta flour</p>
																</td>
																<td
																	class="m_7924699770022590990m_-1635589391856875088item-qty"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px; border-top: 1px solid #dbdbdb; text-align: center"><?php echo $product['product_quantity']; ?></td>
																<td
																	class="m_7924699770022590990m_-1635589391856875088item-price"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px; border-top: 1px solid #dbdbdb; text-align: right">

																	<span
																	class="m_7924699770022590990m_-1635589391856875088price"><?php
															// $item_price= $product['product']->price;
															$item_price = $product ['product_price'];
															$item_qty = $product ['product_quantity'];
															$item_tota_price = $item_price * $item_qty;
															if ($product ['status_s'] == 2) {
																$item_tota_price = 0;
															}
															echo $this->Number->currency ( $item_tota_price, 'LKR' );
															?></span>


																</td>
															</tr>
														</tbody>
														
														<?php endforeach; ?>
														
														
														<tfoot
															class="m_7924699770022590990m_-1635589391856875088order-totals">
															<tr
																class="m_7924699770022590990m_-1635589391856875088subtotal">
																<th colspan="2" scope="row"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; text-align: right; vertical-align: top; padding: 10px; background-color: #f5f5f5; font-weight: 400">
																	Subtotal</th>
																<td
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px; background-color: #f5f5f5; text-align: right">
																	<span
																	class="m_7924699770022590990m_-1635589391856875088price"
																	style="white-space: nowrap"><?php echo $this->Number->currency($total_pdf['subtotal'],'LKR') ?></span>
																</td>
															</tr>
															<tr
																class="m_7924699770022590990m_-1635589391856875088shipping">
																<th colspan="2" scope="row"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; text-align: right; vertical-align: top; padding: 10px; background-color: #f5f5f5; font-weight: 400; padding-top: 0">
																	Shipping &amp; Handling</th>
																<td
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px; background-color: #f5f5f5; text-align: right; padding-top: 0">
																	<span
																	class="m_7924699770022590990m_-1635589391856875088price"
																	style="white-space: nowrap">LKR0.00</span>
																</td>
															</tr>
															<tr
																class="m_7924699770022590990m_-1635589391856875088grand_total">
																<th colspan="2" scope="row"
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; text-align: right; vertical-align: top; padding: 10px; background-color: #f5f5f5; font-weight: 400; padding-top: 0">
																	<strong style="font-weight: 700">Grand Total</strong>
																</th>
																<td
																	style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 10px; background-color: #f5f5f5; text-align: right; padding-top: 0">
																	<strong style="font-weight: 700"><span
																		class="m_7924699770022590990m_-1635589391856875088price"
																		style="white-space: nowrap"><?php echo $this->Number->currency($total_pdf['available'],'LKR') ?></span></strong>
																</td>
															</tr>
														</tfoot>
													</table>
												</td>
											</tr>
										</tbody>
									</table>

								</td>
							</tr>
							<tr>
								<td class="m_7924699770022590990m_-1635589391856875088footer"
									style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding: 25px; background-color: #f5f5f5">
									<span class="HOEnZb"><font color="#888888"> </font></span>
									<table style="border-collapse: collapse; width: 100%">
										<tbody>
											<tr>
												<td
													style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding-bottom: 25px; width: 33%">
													<p style="margin-top: 0; margin-bottom: 0">
														<a href="#m_7924699770022590990_m_-1635589391856875088_"
															style="color: #1979c3; text-decoration: none">About Us</a>
													</p>
													<p style="margin-top: 0; margin-bottom: 0">
														<a href="#m_7924699770022590990_m_-1635589391856875088_"
															style="color: #1979c3; text-decoration: none">Customer
															Service</a>
													</p>
												</td>
												<td
													style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding-bottom: 25px; width: 33%">

													<p class="m_7924699770022590990m_-1635589391856875088phone"
														style="margin-top: 0; margin-bottom: 0; font-size: 18px">
														<a href="tel:0117556600"
															style="color: inherit; text-decoration: none"
															target="_blank">0117556600</a>
													</p>


													<p class="m_7924699770022590990m_-1635589391856875088hours"
														style="margin-top: 0; margin-bottom: 0">
														Hours of Operation:<br> <span
															class="m_7924699770022590990m_-1635589391856875088no-link">9
															am to 6 pm</span>.
													</p>

												</td>
												<td
													style="font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif; vertical-align: top; padding-bottom: 25px; width: 33%">
													<p
														class="m_7924699770022590990m_-1635589391856875088address"
														style="margin-top: 0; margin-bottom: 0">
														Direct 2 door<br> <br> Colombo, ,<br> Sri Lanka
													</p> <span class="HOEnZb"><font color="#888888"> </font></span>
												</td>
											</tr>
										</tbody>
									</table> <span class="HOEnZb"><font color="#888888"> </font></span>
								</td>
							</tr>
						</tbody>
					</table> <span class="HOEnZb"><font color="#888888"> </font></span></td>
			</tr>
		</tbody>
	</table>
	<div class="yj6qo ajU">
		<div id=":n8" class="ajR" role="button" tabindex="0"
			data-tooltip="Show trimmed content" aria-label="Show trimmed content">
			<img class="ajT"
				src="//ssl.gstatic.com/ui/v1/icons/mail/images/cleardot.gif">
		</div>
	</div>
	<span class="HOEnZb adL"><font color="#888888"> </font></span>
</div>