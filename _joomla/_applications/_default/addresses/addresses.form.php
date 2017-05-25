<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__envolute_clients') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$clients = $db->loadObjectList();
?>

<form name="form-<?php echo $APPTAG?>" id="form-<?php echo $APPTAG?>" method="post" enctype="multipart/form-data">

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">
			<?php
				echo JText::_('FORM_TITLE');
				if($cfg['showFormDesc']) :
					echo '<div class="small font-condensed">'.JText::_('FORM_DESCRIPTION').'</div>';
				endif;
			?>
		</h4>
	</div>
	<div class="modal-body">
		<fieldset>
			<div class="row">
				<div id="<?php echo $APPTAG?>-formPaginator" class="col-sm-4 hide">
					<div class="form-group field-required">
						<span class="input-group">
							<span class="input-group-btn">
								<button id="btn-<?php echo $APPTAG?>-prev" class="base-icon-left-open btn btn-sm btn-default" disabled></button>
							</span>
							<input type="text" name="id" id="<?php echo $APPTAG?>-id" class="form-control input-sm" readonly="readonly" />
							<input type="hidden" name="relationId" id="<?php echo $APPTAG?>-relationId" value="<?php echo $_SESSION[$RTAG.'RelId']?>" />
							<input type="hidden" name="<?php echo $APPTAG?>-prev" id="<?php echo $APPTAG?>-prev" />
							<input type="hidden" name="<?php echo $APPTAG?>-next" id="<?php echo $APPTAG?>-next" />
							<span class="input-group-btn">
								<button id="btn-<?php echo $APPTAG?>-next" class="base-icon-right-open btn btn-sm btn-default" disabled></button>
							</span>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-fieldState" class="col-sm-4">
					<div class="form-group">
						<span id="<?php echo $APPTAG?>-state-group" class="btn-group w-full" data-toggle="buttons">
							<label class="col btn btn-sm btn-default btn-active-success strong">
								<span class="base-icon-unset"></span>
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-1" value="1" />
								<?php echo JText::_('TEXT_ACTIVE'); ?>
							</label>
							<label class="col btn btn-sm btn-default btn-active-danger strong">
								<span class="base-icon-unset"></span>
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-0" value="0" /> <?php echo JText::_('TEXT_INACTIVE'); ?>
							</label>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-restart" class="col-sm-4 hide">
					<div class="form-group">
						<button type="button" id="btn-<?php echo $APPTAG?>-restart" class="base-icon-cw btn btn-sm btn-default btn-block">
							 <?php echo JText::_('TEXT_RESTART'); ?>
						</button>
					</div>
				</div>
			</div>

			<hr class="hr-xs" />

			<div class="row">
				<div class="col-sm-5">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DESCRIPTION_DESC'); ?>">
							<?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?>
						</label>
						<div class="input-group">
							<span class="input-group-addon">
								<input type="checkbox" name="main" id="<?php echo $APPTAG?>-main" value="1" class="auto-tab" data-target="addresses-description" data-target-disabled="true" /> <?php echo JText::_('FIELD_LABEL_MAIN'); ?>
							</span>
							<input type="text" name="description" id="<?php echo $APPTAG?>-description" class="form-control upper" disabled="disabled" maxlength="15" />
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_ADDRESS_ZIP_CODE'); ?></label>
						<input type="text" name="zip_code" id="<?php echo $APPTAG?>-zip_code" class="form-control field-cep" />
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STREET'); ?></label>
						<input type="text" name="address" id="<?php echo $APPTAG?>-address" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_ADDRESS_NUMBER'); ?></label>
						<input type="text" name="address_number" id="<?php echo $APPTAG?>-address_number" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_ADDRESS_INFO'); ?></label>
						<input type="text" name="address_info" id="<?php echo $APPTAG?>-address_info" class="form-control" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_ADDRESS_DISTRICT'); ?></label>
						<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_ADDRESS_CITY'); ?></label>
						<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STATE'); ?></label>
						<input type="text" name="address_state" id="<?php echo $APPTAG?>-address_state" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_ADDRESS_COUNTRY'); ?></label>
						<input type="text" name="address_country" id="<?php echo $APPTAG?>-address_country" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label>Latitude</label>
						<input type="text" name="latitude" id="<?php echo $APPTAG?>-latitude" class="form-control" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label>Longitude</label>
						<input type="text" name="longitude" id="<?php echo $APPTAG?>-longitude" class="form-control" />
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ADDRESS_URL_MAP_DESC'); ?>">
							<?php echo JText::_('FIELD_LABEL_ADDRESS_URL_MAP'); ?>
						</label>
						<input type="text" name="url_map" id="<?php echo $APPTAG?>-url_map" class="form-control field-url" />
					</div>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="modal-footer">
		<div class="pull-left bottom-space-xs text-left text-overflow">
			<span class="base-icon-ok-circled2 set-success text-success hide"></span>
			<span class="base-icon-cancel-circled set-error text-danger hide"></span>
			<span class="ajax-loader hide"></span>
		</div>
		<div class="pull-right">
			<button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="base-icon-ok btn btn-success btn-sm" onclick="<?php echo $APPTAG?>_save();"> <?php echo JText::_('TEXT_SAVE'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-save-new" id="btn-<?php echo $APPTAG?>-save-new" class="base-icon-ok btn btn-success btn-sm" onclick="<?php echo $APPTAG?>_save('reset');"> <?php echo JText::_('TEXT_SAVENEW'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-delete" id="btn-<?php echo $APPTAG?>-delete" class="base-icon-trash btn btn-danger btn-sm hide" onclick="<?php echo $APPTAG?>_del(0, true)"> <?php echo JText::_('TEXT_DELETE'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-cancel" id="btn-<?php echo $APPTAG?>-cancel" class="base-icon-cancel btn btn-default btn-sm" data-dismiss="modal"> <?php echo JText::_('TEXT_CANCEL'); ?></button>
		</div>
	</div>

</form>
