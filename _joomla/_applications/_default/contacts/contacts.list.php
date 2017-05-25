<?php
defined('_JEXEC') or die;

// LOAD FILTER
require($PATH_APP_FILE.'.filter.php');

// LIST

	// pagination var's
	$limitDef = !isset($_SESSION[$APPTAG.'plim']) ? $cfg['pagLimit'] : $_SESSION[$APPTAG.'plim'];
	$_SESSION[$APPTAG.'plim']	= $app->input->post->get('list-lim-'.$APPTAG, $limitDef, 'int');
	$lim	= $app->input->get('limit', ($_SESSION[$APPTAG.'plim'] !== 1 ? $_SESSION[$APPTAG.'plim'] : 10000000), 'int');
	$lim0	= $app->input->get('limitstart', 0, 'int');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T3.username') .' user,
			'. $db->quoteName('T2.name') .' grp,
			'. $db->quoteName('T1.type') .',
			'. $db->quoteName('T1.gender') .',
			'. $db->quoteName('T1.name') .',
			'. $db->quoteName('T1.nickname') .',
			'. $db->quoteName('T1.email') .',
			'. $db->quoteName('T1.email_optional') .',
			'. $db->quoteName('T1.birthday') .',
			'. $db->quoteName('T1.occupation') .',
			'. $db->quoteName('T1.description') .',
			'. $db->quoteName('T1.portfolio') .',
			'. $db->quoteName('T1.comment') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON T2.id = T1.group_id
			LEFT JOIN '. $db->quoteName('#__users') .' T3
			ON T3.id = T1.user_id
		WHERE
			'.$where.$orderList;
	;
	try {

		$db->setQuery($query, $lim0, $lim);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();

	} catch (RuntimeException $e) {
		 echo $e->getMessage();
		 return;
	}

// ADMIN VIEW
$adminView = array();
$adminView['head']['info'] = $adminView['head']['actions'] = '';
if($hasAdmin) :
	$adminView['head']['info'] = '
		<th width="30" class="hidden-print"><input type="checkbox" id="'.$APPTAG.'_checkAll" /></th>
		<th width="50" class="hidden-print">'.baseAppHelper::linkOrder('#', 'T1.id', $APPTAG).'</th>
	';
	$adminView['head']['actions'] = '
		<th class="text-center hidden-print" width="60">'.baseAppHelper::linkOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
		<th class="text-center hidden-print" width="100">'.JText::_('TEXT_ACTIONS').'</th>
	';
endif;

// VIEW
$html = '
	<form id="form-list-'.$APPTAG.'" method="post">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					'.$adminView['head']['info'].'
					<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_GROUP'), 'T2.name', $APPTAG).'</th>
					<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_NAME'), 'T1.name', $APPTAG).'</th>
					<th>E-mail</th>
					<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_BIRTHDAY'), 'T1.birthday', $APPTAG).'</th>
					<th>'.JText::_('FIELD_LABEL_USER').'</th>
					'.$adminView['head']['actions'].'
				</tr>
			</thead>
			<tbody>
';

if($num_rows) : // verifica se existe

	// pagination
	$db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
	jimport('joomla.html.pagination');
	$found_rows = $db->loadResult();
	$pageNav = new JPagination($found_rows , $lim0, $lim );

	foreach($res as $item) {

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$listFiles = '';
			for($i = 0; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					$listFiles .= '
						<a href="'.JURI::root(true).'/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
						</a>
					';
				endif;
			}
		endif;

		$adminView['list']['info'] = $adminView['list']['actions'] = '';
		if($hasAdmin) :
			$adminView['list']['info'] = '
				<td class="check-row hidden-print"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" /></td>
				<td class="hidden-print">'.$item->id.'</td>
			';
			$adminView['list']['actions'] = '
				<td class="text-center hidden-print">
					<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
					</a>
				</td>
				<td class="text-center hidden-print">
					<a href="#" class="btn btn-xs btn-success" onclick="contactsaddresses_setRelation('.$item->id.')" data-toggle="modal" data-target="#modal-contactsaddresses" data-backdrop="static" data-keyboard="false"><span class="base-icon-location hasTooltip" title="'.JText::_('MSG_INSERT_ADDRESS').'"></span></a>
					<a href="#" class="btn btn-xs btn-success" onclick="contactsphones_setRelation('.$item->id.')" data-toggle="modal" data-target="#modal-contactsphones" data-backdrop="static" data-keyboard="false"><span class="base-icon-phone hasTooltip" title="'.JText::_('MSG_INSERT_PHONE').'"></span></a>
					<a href="#" class="btn btn-xs btn-success" onclick="contactswebSocials_setRelation('.$item->id.')" data-toggle="modal" data-target="#modal-contactswebSocials" data-backdrop="static" data-keyboard="false"><span class="base-icon-chat-empty hasTooltip" title="'.JText::_('MSG_INSERT_SOCIAL').'"></span></a>
					<a href="#" class="btn btn-xs btn-success" onclick="contactsbanksAccounts_setRelation('.$item->id.')" data-toggle="modal" data-target="#modal-contactsbanksAccounts" data-backdrop="static" data-keyboard="false"><span class="base-icon-bank hasTooltip" title="'.JText::_('MSG_INSERT_BANK_ACCOUNT').'"></span></a>
					<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
					<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
				</td>
			';
		endif;

		$name = !empty($item->nickname) ? $item->nickname : $item->name;
		if($item->type == 0) $gender = '<span '.($item->gender == 1 ? 'class="base-icon-male-symbol cursor-help text-primary hasTooltip" title="'.JText::_('FIELD_LABEL_GENDER_MALE').'"' : 'class="base-icon-female-symbol cursor-help text-danger hasTooltip" title="'.JText::_('FIELD_LABEL_GENDER_FEMALE').'"').'"></span> ';
		else $gender = '<span class="base-icon-building text-live hasTooltip" title="'.JText::_('FIELD_LABEL_TYPE_1').'"></span> ';
		$info = '';
		if(!empty($item->occupation) || !empty($item->description)) :
			$info = !empty($item->occupation) ? ' title="'.baseHelper::nameFormat($item->occupation).'"' : '';
		 	$info .= !empty($item->description) ?  ' data-content="'.$item->description.'"'  : '';
			$info = '<span class="base-icon-info-circled text-live cursor-help hasPopover" data-placement="top"'.$info.'></span> ';
		endif;
		if($item->portfolio == 1) $info .= '<span class="base-icon-star text-live cursor-help hasPopover" data-placement="top" title="'.JText::_('FIELD_LABEL_PORTFOLIO').'" data-content="<span class=\'font-featured\'>&quot;'.$item->comment.'&quot;</span>"></span> ';
		$eopt = !empty($item->email_optional) ? '<br /><span class="small font-featured text-muted cursor-help hasTooltip" title="'.JText::_('TEXT_OPTIONAL').'">('.$item->email_optional.')</span>' : '';
		$rowState = $item->state == 0 ? 'table-danger' : '';
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
				'.$adminView['list']['info'].'
				<td>'.baseHelper::nameFormat($item->grp).'</td>
				<td>
					'.$gender.baseHelper::nameFormat($name).'<br />
					'.$info.'
					<a href="#" class="base-icon-location text-live hasTooltip" onclick="contactsaddresses_listReload(false, false, false, false, false, '.$item->id.')" data-toggle="modal" data-target="#modal-list-contactsaddresses" title="'.JText::_('MSG_VIEW_ADDRESS').'"></a>
					<a href="#" class="base-icon-phone-squared text-live hasTooltip" onclick="contactsphones_listReload(false, false, false, false, false, '.$item->id.')" data-toggle="modal" data-target="#modal-list-contactsphones" title="'.JText::_('MSG_VIEW_PHONE').'"></a>
					<a href="#" class="base-icon-chat-empty text-live hasTooltip" onclick="contactswebSocials_listReload(false, false, false, false, false, '.$item->id.')" data-toggle="modal" data-target="#modal-list-contactswebSocials" title="'.JText::_('MSG_VIEW_SOCIAL').'"></a>
					<a href="#" class="base-icon-bank text-live hasTooltip" onclick="contactsbanksAccounts_listReload(false, false, false, false, false, '.$item->id.')" data-toggle="modal" data-target="#modal-list-contactsbanksAccounts" title="'.JText::_('MSG_VIEW_BANKS_ACCOUNTS').'"></a>
				</td>
				<td>
					'.$item->email.$eopt.'
				</td>
				<td>'.baseHelper::dateFormat($item->birthday, 'd/m/Y', true, '-').'</td>
				<td>'.(!empty($item->user) ? $item->user : '-').'</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="9">
				<div class="alert alert-warning alert-icon m-0">'.JText::_('MSG_LISTNOREG').'</div>
			</td>
		</tr>
	';

endif;

$html .= '
			</tbody>
		</table>
	</form>
';

if($num_rows) :

	// PAGINAÇÃO
		// stats
		$listStart	= $lim0 + 1;
		$listEnd		= $lim0 + $num_rows;
	if($found_rows != $num_rows) :
		$html .= '
			<div class="base-app-pagination pull-left">
				'.$pageNav->getListFooter().'
				<div class="list-stats small text-muted">
					'.JText::sprintf('LIST_STATS', $listStart, $listEnd, $found_rows).'
				</div>
			</div>
		';
	endif;

	$html .= '
		<form id="form-order-'.$APPTAG.'" action="'.$_SERVER['REQUEST_URI'].'" class="pull-right form-inline" method="post">
			<input type="hidden" name="'.$APPTAG.'oF" id="'.$APPTAG.'oF" value="'.$_SESSION[$APPTAG.'oF'].'" />
			<input type="hidden" name="'.$APPTAG.'oT" id="'.$APPTAG.'oT" value="'.$_SESSION[$APPTAG.'oT'].'" />
		</form>
	';

	// ITENS POR PÁGINA
	// seta o parametro 'start = 0' na URL sempre que o limit for refeito
	// isso evita erro quando estiver navegando em páginas subjacentes
	$a = preg_replace("#\?start=.*#", '', $_SERVER['REQUEST_URI']);
	$a = preg_replace("#&start=.*#", '', $a);

	$html .= '
		<form id="form-limit-'.$APPTAG.'" action="'.$a.'" class="pull-right form-inline hidden-print" method="post">
			<label>'.JText::_('LIST_PAGINATION_LIMIT').'</label>
			<select name="list-lim-'.$APPTAG.'" onchange="'.$APPTAG.'_setListLimit()">
				<option value="5" '.($_SESSION[$APPTAG.'plim'] === 5 ? 'selected' : '').'>5</option>
				<option value="20" '.($_SESSION[$APPTAG.'plim'] === 20 ? 'selected' : '').'>20</option>
				<option value="50" '.($_SESSION[$APPTAG.'plim'] === 50 ? 'selected' : '').'>50</option>
				<option value="100" '.($_SESSION[$APPTAG.'plim'] === 100 ? 'selected' : '').'>100</option>
				<option value="1" '.($_SESSION[$APPTAG.'plim'] === 1 ? 'selected' : '').'>Todos</option>
			</select>
		</form>
	';

endif;

return $htmlFilter.$html;

?>
