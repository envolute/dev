<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// GROUP -> select
	$fGroup	= $app->input->get('fGroup', 0, 'int');
	if($fGroup != 0) $where .= ' AND '.$db->quoteName('T1.group_id').' = '.$fGroup;
	// GENDER -> select
	$gender	= $app->input->get('fGender', 3, 'int');
	$where .= ($gender == 3) ? ' AND '. $db->quoteName('T1.gender').' != '.$gender : ' AND '. $db->quoteName('T1.gender').' = '.$gender;
	// MARITAL STATUS -> select
	$fMStatus	= $app->input->get('fMStatus', '', 'string');
	if(!empty($fMStatus)) $where .= ' AND '.$db->quoteName('T1.marital_status').' = '.$db->quote($fMStatus);
	// BLOOD TYPE -> select
	$fBt	= $app->input->get('fBt', '', 'string');
	if(!empty($fBt)) $where .= ' AND '.$db->quoteName('T1.blood_type').' = '.$db->quote($fBt);
	// DRIVER's CARD -> select
	$fDriver	= $app->input->get('fDriver', 'X', 'string');
	if($fDriver != 'X') $where .= ' AND '.$db->quoteName('T1.driver').' = '.$db->quote($fDriver);
	// NOT USERS -> checkbox
	$fUser	= $app->input->get('fUser', 0, 'int');
	if($fUser == 1) $where .= ' AND '.$db->quoteName('T3.username').' IS NULL';
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.birthday').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.email').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.place_birth').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.cpf').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.rg').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.pis').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.ctps').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.occupation').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = ''; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T1.name';
			$_SESSION[$APPTAG.'oT'] = 'ASC';
	endif;
	if(!empty($ordf)) :
		$_SESSION[$APPTAG.'oF'] = $ordf;
		$_SESSION[$APPTAG.'oT'] = $ordt;
	endif;
	$orderList = !empty($_SESSION[$APPTAG.'oF']) ? $db->quoteName($_SESSION[$APPTAG.'oF']).' '.$_SESSION[$APPTAG.'oT'] : '';
	// fixa a ordenação em caso de itens com o mesmo valor (ex: mesma data)
	$orderList .= (!empty($orderList) && !empty($orderDef) ? ', ' : '').$orderDef;
	$orderList .= (!empty($orderList) ? ', ' : '').$db->quoteName('T1.id').' DESC';
	// set order by
	$orderList = !empty($orderList) ? ' ORDER BY '.$orderList : '';

	$SETOrder = $APPTAG.'setOrder';
	$$SETOrder = function($title, $col, $APPTAG) {
		$tp = 'ASC';
		$icon = '';
		if($col == $_SESSION[$APPTAG.'oF']) :
			$tp = ($_SESSION[$APPTAG.'oT'] == 'DESC' || empty($_SESSION[$APPTAG.'oT'])) ? 'ASC' : 'DESC';
			$icon = ' <span class="'.($tp == 'ASC' ? 'base-icon-down-dir' : 'base-icon-up-dir').'"></span>';
		endif;
		return '<a href="#" onclick="'.$APPTAG.'_setListOrder(\''.$col.'\', \''.$tp.'\')">'.$title.$icon.'</a>';
	};

// FILTER'S DINAMIC FIELDS

	// types -> select
  $flt_group = '';
  $query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' ORDER BY id';
  $db->setQuery($query);
  $grps = $db->loadObjectList();
  foreach ($grps as $obj) {
    $flt_group .= '<option value="'.$obj->id.'"'.($obj->id == $fGroup ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
  }

	// due date -> select
	$due_option = '';
	for($i = 1; $i <= 31; $i++) {
		$d = $i < 10 ? '0'.$i : $i;
		$due_option .= '<option value="'.$d.'"'.($d == $fDue ? ' selected = "selected"' : '').'>'.$d.'</option>';
	}

// CUSTOM -> menssagem de successo da sincronização
$sync = $app->input->get('sync', 0, 'int');
$alertSync = '';
if($sync == 1) :
	$alertSync = '
	<div class="alert alert-success alert-icon">
		'.JText::_('TEXT_SYNCRONIZED').JText::_('TEXT_SYNCRONIZED_DESC').'
	</div>
	';
endif;

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fGroup" id="fGroup" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_GROUP').' -</option>
							'.$flt_group.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fGender" id="fGender" class="form-control input-sm set-filter">
							<option value="3">- '.JText::_('FIELD_LABEL_GENDER').' -</option>
							<option value="1"'.($gender == 1 ? ' selected' : '').'>'.JText::_('TEXT_MALE').'</option>
							<option value="2"'.($gender == 2 ? ' selected' : '').'>'.JText::_('TEXT_FEMALE').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fMStatus" id="fMStatus" class="form-control input-sm set-filter">
							<option value="">'.JText::_('FIELD_LABEL_MARITAL_STATUS').'</option>
							<option value="SOLTEIRO"'.($fMStatus == 'SOLTEIRO' ? ' selected' : '').'>Solteiro</option>
							<option value="CASADO"'.($fMStatus == 'CASADO' ? ' selected' : '').'>Casado</option>
							<option value="UNIÃO ESTÁVEL"'.($fMStatus == 'UNIÃO ESTÁVEL' ? ' selected' : '').'>União Estável</option>
							<option value="DIVORCIADO"'.($fMStatus == 'DIVORCIADO' ? ' selected' : '').'>Divorciado</option>
							<option value="VIÚVO"'.($fMStatus == 'VIÚVO' ? ' selected' : '').'>Viúvo</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fBt" id="fBt" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_BLOOD').' -</option>
							<option value="O+"'.($fBt == 'O+' ? ' selected' : '').'>O+</option>
							<option value="A+"'.($fBt == 'A+' ? ' selected' : '').'>A+</option>
							<option value="B+"'.($fBt == 'B+' ? ' selected' : '').'>B+</option>
							<option value="O-"'.($fBt == 'O-' ? ' selected' : '').'>O-</option>
							<option value="A-"'.($fBt == 'A-' ? ' selected' : '').'>A-</option>
							<option value="B-"'.($fBt == 'B-' ? ' selected' : '').'>B-</option>
							<option value="AB+"'.($fBt == 'AB+' ? ' selected' : '').'>AB+</option>
							<option value="AB-"'.($fBt == 'AB-' ? ' selected' : '').'>AB-</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fDriver" id="fDriver" class="form-control input-sm set-filter">
							<option value="X">- '.JText::_('FIELD_LABEL_DRIVER_CARD').' -</option>
							<option value=""'.($fDriver == '' ? ' selected' : '').'>'.JText::_('TEXT_HAS_NOT').'</option>
							<option value="A"'.($fDriver == 'A' ? ' selected' : '').'>A</option>
							<option value="B"'.($fDriver == 'B' ? ' selected' : '').'>B</option>
							<option value="C"'.($fDriver == 'C' ? ' selected' : '').'>C</option>
							<option value="D"'.($fDriver == 'D' ? ' selected' : '').'>D</option>
							<option value="E"'.($fDriver == 'E' ? ' selected' : '').'>E</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<div class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-sm btn-warning btn-active-danger set-filter hasTooltip" title="'.JText::_('TEXT_NOT_USERS_DESC').'">
								<span class="base-icon-cancel btn-icon"></span>
								<input type="checkbox" name="fUser" value="1"'.($fUser == 1 ? ' checked' : '').' /> '.JText::_('TEXT_NOT_USERS').'
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="active" id="active" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<span class="input-group">
              <span class="input-group-addon strong">'.JText::_('FIELD_LABEL_DATE').'</span>
							<input type="text" name="dateMin" value="'.$dateMin.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateMax" value="'.$dateMax.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
            <input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group text-right">
						<span class="btn-group">
							<button type="submit" class="btn btn-sm btn-primary">
                <span class="base-icon-search btn-icon"></span> '.JText::_('TEXT_SEARCH').'
              </button>
							<a href="'.JURI::current().'" class="base-icon-cancel-circled btn btn-sm btn-danger hasTooltip" title="'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'"></a>
						</span>
					</div>
				</div>
			</div>
		</fieldset>
		'.$alertSync.'
	</form>
';

?>
