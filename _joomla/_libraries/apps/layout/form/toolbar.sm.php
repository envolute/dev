<div class="row form-toolbar mb-3">
  <div id="<?php echo $APPTAG?>-formPaginator" class="col-sm">
    <div class="form-group formPaginator-pager" hidden>
      <span class="input-group">
        <span class="input-group-btn">
          <button id="btn-<?php echo $APPTAG?>-prev" class="base-icon-left-open btn btn-sm btn-default" disabled></button>
        </span>
        <input type="text" name="displayId" id="<?php echo $APPTAG?>-displayId" class="form-control form-control-sm" placeholder="ID" />
        <input type="hidden" name="id" id="<?php echo $APPTAG?>-id" />
        <input type="hidden" name="relationId" id="<?php echo $APPTAG?>-relationId" value="<?php echo $_SESSION[$RTAG.'RelId']?>" />
        <input type="hidden" name="<?php echo $APPTAG?>-prev" id="<?php echo $APPTAG?>-prev" />
        <input type="hidden" name="<?php echo $APPTAG?>-next" id="<?php echo $APPTAG?>-next" />
        <span class="input-group-btn">
          <button id="btn-<?php echo $APPTAG?>-next" class="base-icon-right-open btn btn-sm btn-default" disabled></button>
        </span>
      </span>
    </div>
  </div>
  <div id="<?php echo $APPTAG?>-fieldState" class="col-sm">
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
</div>
