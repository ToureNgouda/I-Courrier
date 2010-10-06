<?php
/* View */
if($mode == "list"){
	list_show::admin_list(
					$docserver_types_list['tab'], 
					count($docserver_types_list['tab']), 
					$docserver_types_list['title'], 
					'docserver_type_id',
					'docserver_types_management_controler&mode=list',
					'docservers','docserver_type_id', 
					true, 
					$docserver_types_list['page_name_up'], 
					$docserver_types_list['page_name_val'], 
					$docserver_types_list['page_name_ban'], 
					$docserver_types_list['page_name_del'], 
					$docserver_types_list['page_name_add'], 
					$docserver_types_list['label_add'], 
					false, 
					false, 
					_ALL_DOCSERVER_TYPES, 
					_DOCSERVER_TYPE, 
					$_SESSION['config']['businessappurl'].'static.php?filename=manage_docserver_types.gif&admin=docservers', 
					false, 
					true, 
					false, 
					true, 
					$docserver_types_list['what'], 
					true, 
					$docserver_types_list['autoCompletionArray']
				);
}
elseif($mode == "up" || $mode == "add"){
	?><script type="text/javascript" src="<?php  echo $_SESSION['config']['businessappurl'];?>static.php?filename=docserver_types_management.js"></script>
	<h1><img src="<?php  echo $_SESSION['config']['businessappurl'];?>static.php?filename=manage_docserver_b.gif" alt="" />
		<?php
		if($mode == "add"){
			echo _DOCSERVER_TYPE_ADDITION;
		}
		elseif($mode == "up"){
			echo _DOCSERVER_TYPE_MODIFICATION;
		}
		?>
	</h1>
	<div id="inner_content" class="clearfix" align="center">
		<br><br>
		<?php
		if($state == false){
			echo "<br /><br />"._THE_DOCSERVER_TYPE." "._UNKOWN."<br /><br /><br /><br />";
		} else {
		 	if($mode == "up") {
				if(count($docservers)>0) { 
					?>
					<div onclick="new Effect.toggle('users_list', 'blind', {delay:0.2});return false;" >
					&nbsp;<img src="<?php  echo $_SESSION['config']['businessappurl'];?>static.php?filename=manage_doctypes_b.gif" alt="" /><i><?php  echo _SEE_DOCSERVERS_;?></i><img src="<?php echo $_SESSION['config']['businessappurl'];?>static.php?filename=plus.png" alt="" />
					<span class="lb1-details">&nbsp;</span></div>
					<div class="desc" id="users_list" style="display:none;">
						<div class="ref-unit">
							<table cellpadding="0" cellspacing="0" border="0" class="listingsmall" summary="">
								<thead>
									<tr>
										<th><?php  echo _DOCSERVER_ID;?></th>
										<th ><?php  echo _DEVICE_LABEL;?></th>
										<th ><?php  echo _DOCSERVER_LOCATION_ID;?></th>
									</tr>
								</thead>

							<tbody>
							 <?php
							$color = ' class="col"';
							 for($i=0;$i<count($docservers);$i++) {
								 
									if($color == ' class="col"') {
										$color = '';
									} else {
										$color = ' class="col"';
									}
										?>
								 <tr <?php  echo $color; ?> >
											<td style="width:25%;"><?php  echo $docservers[$i]->__get('docserver_id');?></td>
											<td style="width:25%;"><?php  echo $docservers[$i]->__get('device_label');?></td>
											<td style="width:25%;"><?php  echo $docservers[$i]->__get('docserver_location_id');?></td>
										   <td ><?php 
											if(core_tools::test_service('admin_docservers', 'apps', false)) {?>
										   <a class="change" href="<?php echo $_SESSION['config']['businessappurl'].'index.php?page=docservers_management_controler&amp;mode=up&amp;admin=docservers&amp;id='.$docservers[$i]->__get('docserver_id'); ?>"  title="<?php echo _GO_MANAGE_DOCSERVER;?>"><i><?php echo _GO_MANAGE_DOCSERVER;?></i></a><?php }?></td>
								</tr>
									<?php
								}
							?>
							</tbody>
							</table>
						<br/>
						</div>
					</div>
			<?php 
			}
		}?>
			<br/><br/>
			<form name="formdocserver" method="post" class="forms" action="<?php echo $_SESSION['config']['businessappurl']."index.php?display=true&page=docserver_types_management_controler&admin=docservers&mode=".$mode;?>">
				<input type="hidden" name="display" value="value" />
				<input type="hidden" name="admin" value="docservers" />
				<input type="hidden" name="page" value="docserver_types_management_controler" />
				<input type="hidden" name="mode" id="mode" value="<?php echo $mode;?>" />
				<input type="hidden" name="order" id="order" value="<?php echo $_REQUEST['order'];?>" />
				<input type="hidden" name="order_field" id="order_field" value="<?php echo $_REQUEST['order_field'];?>" />
				<input type="hidden" name="what" id="what" value="<?php echo $_REQUEST['what'];?>" />
				<input type="hidden" name="start" id="start" value="<?php echo $_REQUEST['start'];?>" />
				<p>
				 	<label for="id"><?php echo _DOCSERVER_TYPE_ID; ?> : </label>
					<input name="id" type="text"  id="id" value="<?php echo functions::show($_SESSION['m_admin']['docserver_types']['docserver_type_id']); ?>" <?php if($mode == "up") echo " readonly='readonly' class='readonly'";?>/>
				</p>
				<p>
				 	<label for="docserver_type_label"><?php echo _DOCSERVER_TYPE_LABEL; ?> : </label>
					<input name="docserver_type_label" type="text"  id="docserver_type_label" value="<?php echo functions::show($_SESSION['m_admin']['docserver_types']['docserver_type_label']); ?>"/>
				</p>
	           	<p>
	                <label><?php echo _IS_CONTAINER; ?> : </label>
	                <input type="radio" class="check" name="is_container" id="is_container" value="true" <?php if($_SESSION['m_admin']['docserver_types']['is_container']){?> checked="checked"<?php } ?> onclick="hide_index(false, 'container_max_number');"/><?php echo _YES;?>
	                <input type="radio" class="check" name="is_container" id="is_container" value="false" <?php if(!$_SESSION['m_admin']['docserver_types']['is_container'] || $_SESSION['m_admin']['docserver_types']['is_container'] == ''){?> checked="checked"<?php } ?> onclick="hide_index(true, 'container_max_number');"/><?php echo _NO;?>
	            </p>
	            <div class ="container_max_number" id="container_max_number">
				<p>
				 	<label for="container_max_number"><?php echo _CONTAINER_MAX_NUMBER; ?> : </label>
					<input name="container_max_number" type="text"  id="container_max_number" value="<?php echo functions::show($_SESSION['m_admin']['docserver_types']['container_max_number']); ?>"/>
				</p>
				</div>
				<p>
	                <label><?php echo _IS_COMPRESSED; ?> : </label>
	                <input type="radio" class="check" name="is_compressed" id="is_compressed" value="true" <?php if($_SESSION['m_admin']['docserver_types']['is_compressed']){?> checked="checked"<?php } ?> onclick="hide_index(false, 'compression_mode');"/><?php echo _YES;?>
	                <input type="radio" class="check" name="is_compressed" id="is_compressed" value="false" <?php if(!$_SESSION['m_admin']['docserver_types']['is_compressed'] || $_SESSION['m_admin']['docserver_types']['is_compressed'] == ''){?> checked="checked"<?php } ?> onclick="hide_index(true, 'compression_mode');"/><?php echo _NO;?>
	            </p>
	            <div class ="compression_mode" id="compression_mode">
				<p>
					<label for="compression_mode"><?php echo _COMPRESS_MODE; ?> : </label>
					<select name="compression_mode">
						<?php
						for($cptCompressMode=1;$cptCompressMode<count($_SESSION['docserversFeatures']['DOCSERVERS']['COMPRESS']['MODE']);$cptCompressMode++){
							?>
							<option value="<?php echo $_SESSION['docserversFeatures']['DOCSERVERS']['COMPRESS']['MODE'][$cptCompressMode];?>" <?php if($_SESSION['m_admin']['docserver_types']['compression_mode'] == $_SESSION['docserversFeatures']['DOCSERVERS']['COMPRESS']['MODE'][$cptCompressMode]) { echo 'selected="selected"';}?>><?php echo $_SESSION['docserversFeatures']['DOCSERVERS']['COMPRESS']['MODE'][$cptCompressMode];?></option>
							<?php
						}
						?>
					</select>
				</p>
				</div>
				<p>
	                <label><?php echo _IS_META; ?> : </label>
	                <input type="radio" class="check" name="is_meta" id="is_meta" value="true" <?php if($_SESSION['m_admin']['docserver_types']['is_meta']){?> checked="checked"<?php } ?> onclick="hide_index(false, 'meta_template');"/><?php echo _YES;?>
	                <input type="radio" class="check" name="is_meta" id="is_meta" value="false"  <?php if(!$_SESSION['m_admin']['docserver_types']['is_meta'] || $_SESSION['m_admin']['docserver_types']['is_meta'] == ''){?> checked="checked"<?php } ?> onclick="hide_index(true, 'meta_template');"/><?php echo _NO;?>
	            </p>
	            <div class ="meta_template" id="meta_template">
				<p>
					<label for="meta_template"><?php echo _META_TEMPLATE; ?> : </label>
					<select name="meta_template" id="meta_template">
						<?php
						for($cptCompressMode=1;$cptCompressMode<count($_SESSION['docserversFeatures']['DOCSERVERS']['META_TEMPLATE']['MODE']);$cptCompressMode++){
							?>
							<option value="<?php echo $_SESSION['docserversFeatures']['DOCSERVERS']['META_TEMPLATE']['MODE'][$cptCompressMode];?>" <?php if($_SESSION['m_admin']['docserver_types']['meta_template'] == $_SESSION['docserversFeatures']['DOCSERVERS']['META_TEMPLATE']['MODE'][$cptCompressMode]) { echo 'selected="selected"';}?>><?php echo $_SESSION['docserversFeatures']['DOCSERVERS']['META_TEMPLATE']['MODE'][$cptCompressMode];?></option>
							<?php
						}
						?>
					</select>
				</p>
				</div>
				<p>
	                <label><?php echo _IS_LOGGED; ?> : </label>
	                <input type="radio" class="check" name="is_logged" id="is_logged" value="true" <?php if($_SESSION['m_admin']['docserver_types']['is_logged']){?> checked="checked"<?php } ?> onclick="hide_index(false, 'log_template');"/><?php echo _YES;?>
	                <input type="radio" class="check" name="is_logged" id="is_logged" value="false" <?php if(!$_SESSION['m_admin']['docserver_types']['is_logged'] || $_SESSION['m_admin']['docserver_types']['is_logged'] == ''){?> checked="checked"<?php } ?> onclick="hide_index(true, 'log_template');"/><?php echo _NO;?>
	            </p>
	            <div class ="log_template" id="log_template">
				<p>
					<label for="log_template"><?php echo _LOG_TEMPLATE; ?> : </label>
					<select name="log_template" id="log_template">
						<?php
							for($cptCompressMode=1;$cptCompressMode<count($_SESSION['docserversFeatures']['DOCSERVERS']['LOG_TEMPLATE']['MODE']);$cptCompressMode++){
								?>
								<option value="<?php echo $_SESSION['docserversFeatures']['DOCSERVERS']['LOG_TEMPLATE']['MODE'][$cptCompressMode];?>" <?php if($_SESSION['m_admin']['docserver_types']['log_template'] == $_SESSION['docserversFeatures']['DOCSERVERS']['LOG_TEMPLATE']['MODE'][$cptCompressMode]) { echo 'selected="selected"';}?>><?php echo $_SESSION['docserversFeatures']['DOCSERVERS']['LOG_TEMPLATE']['MODE'][$cptCompressMode];?></option>
								<?php
							}
						?>
					</select>
				</p>
				</div>
				<p>
	                <label><?php echo _IS_SIGNED; ?> : </label>
	                <input type="radio" class="check" name="is_signed" id="is_signed" value="true" <?php if($_SESSION['m_admin']['docserver_types']['is_signed']){?> checked="checked"<?php } ?> onclick="hide_index(false, 'signature_mode');"/><?php echo _YES;?>
	                <input type="radio" class="check" name="is_signed" id="is_signed" value="false" <?php if(!$_SESSION['m_admin']['docserver_types']['is_signed'] || $_SESSION['m_admin']['docserver_types']['is_signed'] == ''){?> checked="checked"<?php } ?> onclick="hide_index(true, 'signature_mode');"/><?php echo _NO;?>
	            </p>
	            <div class ="signature_mode" id="signature_mode">
				<p>
					<label for="signature_mode"><?php echo _SIGNATURE_MODE; ?> : </label>
					<select name="signature_mode" id="signature_mode">
						<?php
						for($cptCompressMode=1;$cptCompressMode<count($_SESSION['docserversFeatures']['DOCSERVERS']['SIGNATURE_MODE']['MODE']);$cptCompressMode++){
							?>
							<option value="<?php echo $_SESSION['docserversFeatures']['DOCSERVERS']['SIGNATURE_MODE']['MODE'][$cptCompressMode];?>" <?php if($_SESSION['m_admin']['docserver_types']['signature_mode'] == $_SESSION['docserversFeatures']['DOCSERVERS']['SIGNATURE_MODE']['MODE'][$cptCompressMode]) { echo 'selected="selected"';}?>><?php echo $_SESSION['docserversFeatures']['DOCSERVERS']['SIGNATURE_MODE']['MODE'][$cptCompressMode];?></option>
						<?php
						}
						?>
					</select>
				</p>
				</div>
				<p class="buttons">
					<?php
					if($mode == "up"){
						?>
						<input class="button" type="submit" name="submit" value="<?php echo _MODIFY; ?>" />
						<?php
					}
					elseif($mode == "add"){
						?>
						<input type="submit" class="button"  name="submit" value="<?php echo _ADD; ?>" />
						<?php
					}
					?>
	               <input type="button" class="button"  name="cancel" value="<?php echo _CANCEL; ?>" onclick="javascript:window.location.href='<?php echo $_SESSION['config']['businessappurl'];?>index.php?page=docserver_types_management_controler&amp;admin=docservers&amp;mode=list';"/>
				</p>
			</form>
			<script type="text/javascript">
				//on load hide inputs
				<?php
				if(!$_SESSION['m_admin']['docserver_types']['is_container']) {
					?>
					hide_index(true, 'container_max_number');
					<?php
				}
				if(!$_SESSION['m_admin']['docserver_types']['is_compressed']) {
					?>
					hide_index(true, 'compression_mode');
					<?php
				}
				if(!$_SESSION['m_admin']['docserver_types']['is_meta']) {
					?>
					hide_index(true, 'meta_template');
					<?php
				}
				if(!$_SESSION['m_admin']['docserver_types']['is_logged']) {
					?>
					hide_index(true, 'log_template');
					<?php
				}
				if(!$_SESSION['m_admin']['docserver_types']['is_signed']) {
					?>
					hide_index(true, 'signature_mode');
					<?php
				}
				?>
			</script>
			<?php
		}
		?>
	</div>
	<?php
}
?>
