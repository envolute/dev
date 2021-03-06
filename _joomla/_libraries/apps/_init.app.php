<?php

// ACCESS
// Grupos de acesso declarados
if(isset($cfg['groupId']) && ($cfg['groupId']['viewer'][0] != 0 || $cfg['groupId']['admin'][0] != 0)) :
	$viewer = array_merge($cfg['groupId']['viewer'], $cfg['groupId']['admin']);
	$hasGroup = array_intersect($groups, $viewer); // se está na lista de grupos permitidos
	$hasAdmin = array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos
// Se o acesso não for público e os grupos não forem definidos
// indica que o acesso é disponível a qualquer grupo restrito
elseif(!$user->guest) :
	$hasGroup = $hasAdmin = true;
endif;
if(!$cfg['isPublic']) :
	if($user->guest) :
		$app->redirect(JURI::root(true).'/login?return='.urlencode(base64_encode(JURI::current())));
		exit();
	elseif(!$hasGroup && !$hasAdmin) :
		$app->enqueueMessage(JText::_('MSG_NOT_PERMISSION'), 'warning');
		$app->redirect(JURI::root(true));
		exit();
	endif;
endif;

// LOAD SCRIPTS
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base(true).'/templates/base/css/style.app.css');
$doc->addScript(JURI::base(true).'/templates/base/js/forms.js');
$doc->addScript(JURI::base(true).'/templates/base/js/validate.js');
// Carrega Jquery.ui datepicker;
if($cfg['dateConvert'] || $cfg['load_UI']) {
	$doc->addStyleSheet(JURI::base(true).'/templates/base/libs/template/jquery/jquery-ui.min.css');
	$doc->addScript(JURI::base(true).'/templates/base/libs/template/jquery/jquery-ui.min.js');
}
if($cfg['htmlEditor']) {
	$doc->addStyleSheet(JURI::base(true).'/templates/base/libs/forms/editor/ui/trumbowyg.min.css');
	$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/trumbowyg.min.js');
	$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/langs/pt.min.js');
	if($cfg['htmlEditorFull']) {
		// Base64
		$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/plugins/base64/trumbowyg.base64.min.js');
		// Colors
		$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/plugins/colors/trumbowyg.colors.min.js');
		$doc->addStyleSheet(JURI::base(true).'/templates/base/libs/forms/editor/plugins/colors/ui/trumbowyg.colors.min.css');
		// Noembed
		$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/plugins/noembed/trumbowyg.noembed.min.js');
	}
}

?>
