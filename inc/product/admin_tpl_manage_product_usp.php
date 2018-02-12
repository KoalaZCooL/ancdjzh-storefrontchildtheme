<div class="import-jde-products-form-container">
<?php
if ( isset($resp) && !empty($resp) ) {?>
	<div><p style="background-color: green; color: white;padding: 1em;">Settings Successfully Saved</p></div>
<?php }?>
<h2>ANC Product USP</h2>
<form name="anc-manage-usp" action="<?php echo admin_url('admin.php?page=anc-manage-usp') ?>" method="post">
	<table>
	<tbody>
		<tr>
			<th>#</th>
			<th>USP</th>
		</tr>
		<?php for($i = 0; $i<4; $i++){?>
		<tr>
			<td><?=$i+1?></td>
			<td><input type="text" name="opts[<?=$i?>]" value="<?=empty($opts[$i])?'':$opts[$i]?>" /></td>
		</tr>
		<?php }?>
	</tbody>
	</table>
<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save'); ?>" /></p>

</form>
</div>