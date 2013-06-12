<style>
	div.file-uploader select, input#word_count{
		min-width: 250px;
	}
</style>

<?php 
	$file_types = array('Word', 'TeX', 'PDF', 'PPT', 'Excel', 'Images');
?>
<div class="file-uploader">
	<table class="form-table">
		<tr>
			<th scope="row">Your country *</th>
			<td>
				<select name="country">
					<option value="USA">USA</option>
					<option value="UK">UK</option>
				</select>
			</td>
		</tr>
		
		<tr> 
			<th scope="row">Your manuscript's file type *</th>
			<td>
				<?php 
					foreach($file_types as $key => $type){
						?>
						<input id="<?php echo 'file_type_' . $key; ?>" type="radio" name="file_type" value="<?php echo $key +1; ?>" /> <label for="<?php echo 'file_type_' . $key; ?>"><?php echo $type; ?> </label> &nbsp; &nbsp;
						<?php 
					}
				?>
			</td> 
		</tr>
		
		<tr>
			<th>Show me *</th>
			<td>
				<input type="radio" name="show_me" value="1" id="show_me_1" /> <label for="show_me_1"> Low-price plans first </label> &nbsp; &nbsp;
				<input type="radio" name="show_me" value="2" id="show_me_2" /> <label for="show_me_2"> Fastest plans first </label>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label for="word_count"> Word count for your manuscript *</label></th>
			<td> <input type="text" name="word_count" value="" id="word_count" /> <input type="button" name="search" value="search" class="button-primary button"> </td>
		</tr>
		
	</table>
	
	<!-- 
	<div class="Paypal-message" style="display:none"> Wait ... </div>
	<p id="paypal-payment"> <input type="button" value="Pay with Paypal" class="button button-primary" /></p>
	 -->
	
</div>