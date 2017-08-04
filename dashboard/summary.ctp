<!--<div class="right_col" role="main">-->
<div class="">
<?php /*?>
            <div class="row top_tiles" style="margin: 10px 0;">
              <div class="col-md-4 col-sm-4 col-xs-4 tile">
                <span>Total sales</span>
                <h2><?php echo $total_sum; ?></h2>
                <span class="sparkline_one" style="height: 160px;">
                      <canvas width="340" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
                  </span>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-4 tile">
                <span>Total no of orders</span>
                <h2><?php echo $order_count; ?></h2>
                <span class="sparkline_two" style="height: 160px;">
                      <canvas width="340" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
                  </span>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-4 tile">
                <span>Total customers</span>
                <h2><?php echo $customer_count; ?></h2>
                <span class="sparkline_three" style="height: 160px;">
                      <canvas width="340" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
                  </span>
              </div>
          </div>
            <br />
			<?php */?>
			
			<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="dashboard_graph">
            <div class="row x_title">
				<?php if($from!=null && $to!=null):?>
                <div class="col-md-6">
					<h2>Order Details: <?php echo $from.' to '.$to;?></h2>
				</div>
				<?php endif;?>
				<?php if($message!=null):?>
                <div class="col-md-6">
					<h2><?php echo $message;?></h2>
				</div>
				<?php endif;?>				
                <div class="col-md-6 <?php echo (($from==null || $to==null) && $message==null)? 'col-md-offset-6':''; ?>">
                    <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                      <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                      <span>December 30, 2014 - January 28, 2015</span> <b class="caret"></b>
                    </div>
                </div>
            </div>
			
			
			<div class="row top_tiles" style="margin: 10px 0;">
				  <div class="col-md-4 col-sm-4 col-xs-4 tile">
					<span><i class="fa fa-money"></i> Total sales</span>
					<h2><?php echo $total_sum; ?></h2>
					<!--<span class="sparkline_one" style="height: 160px;">
						  <canvas width="340" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
					  </span>-->
				  </div>
				  <div class="col-md-4 col-sm-4 col-xs-4 tile">
					<span><i class="fa fa-edit"></i> Total no of orders</span>
					<h2><?php echo $order_count; ?></h2>
					<!--<span class="sparkline_two" style="height: 160px;">
						  <canvas width="340" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
					  </span>-->
				  </div>
				  <div class="col-md-4 col-sm-4 col-xs-4 tile">
					<span><i class="fa fa-user"></i> Total customers</span>
					<h2><?php echo $customer_count; ?></h2>
					<!--<span class="sparkline_three" style="height: 160px;">
						  <canvas width="340" height="60" style="display: inline-block; vertical-align: top; width: 94px; height: 30px;"></canvas>
					  </span>-->
				  </div>
            </div>
			
			
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<br>
			
			<div class="row">
              

              <!-- bar charts group -->
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Sales <?php echo $from==null || $to == null ? '- Last ten days': ''; ?></h2>                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content1">
                    <div id="graph_bar_group" style="width:100%; height:280px;"></div>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <!-- /bar charts group -->

              

              
            </div>
			
			<div class="row">
              <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Number of Orders <?php echo $from==null || $to == null ? '- Today': ''; ?></h2>
                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <div id="echart_pie" style="height:350px;"></div>

                  </div>
                </div>
              </div>
              <div class="col-md-8 col-sm-8 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Sales <?php echo $from==null || $to == null ? '- Last ten days': ''; ?></h2>
                    
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">

                    <div id="echart_line" style="height:350px;"></div>

                  </div>
                </div>
              </div>
            </div>

            
          </div>
        <!--</div>-->