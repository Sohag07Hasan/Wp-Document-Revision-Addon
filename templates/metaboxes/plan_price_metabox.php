
<?php 
	global $post;
	$plan_price = self::get_plans_prices();	
?>

<div class="plan_and_price">
	
	<?php 
		foreach(self::$plans as $key => $plan){
			?>
			<div class="plan-holder">
				<strong class="plan-heading"><?php echo $plan[1]; ?></strong> <br/>
				<span class="plan-describing"><?php echo $plan_price['plan'][$plan[0]]; ?></span>
				
				<table>
					<thead>
						<tr>
							<th>Delivery on (GMT)</th> <th>Price (USD)</th> <th> &nbsp; </th> <th> &nbsp; </th>
						</tr>
					</thead>
					
					<tbody>
						<tr>
							<td><?php echo date('d M, Y h:i A', current_time('timestamp')); ?></td>
							<td plan="<?php echo $plan[0] ?>"> $0.00 </td>
							<td>Proforma Invoice</td>
							<td><input plan_button="final-step-for-payment" plan="<?php echo $plan[0]; ?>" post_id="<?php echo $post->ID; ?>" type="button" class="button button-primary" value="Select" /></td>
						</tr>
					</tbody>
					
				</table>
					
			</div>
			<?php 
		}
	?>
	
	<div class="delivery-time">
		<h4>Select an interval</h4>
		<?php 
			foreach(self::$intervals as $key => $interval){
				?>
				<input <?php if($key == 0) echo 'checked'; ?> type="radio" name="interval" id="<?php echo $interval[0] . '_interval'; ?>" value="<?php echo $interval[0]; ?>" /> <label for="<?php echo $interval[0] . '_interval'; ?>"><?php echo $interval[1]; ?></label> &nbsp; &nbsp;
				<?php 
			}
		?>
	</div>
	
</div>