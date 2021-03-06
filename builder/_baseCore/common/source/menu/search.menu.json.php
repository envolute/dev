<?php

header( 'Cache-Control: no-cache' );
header( 'content-type: application/json; charset=utf-8' );

function is_valid_callback($subject) {
    $identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

    $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
      'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
      'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
      'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
      'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
      'private', 'public', 'yield', 'interface', 'package', 'protected',
      'static', 'null', 'true', 'false');

    return preg_match($identifier_syntax, $subject)
        && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
}

//if(isset($_REQUEST['search']) && !empty($_REQUEST['search'])) :

	/* LOAD JOOMLA FRAMEWORK */
	include_once('../load.joomla.php');

	$app = JFactory::getApplication(site);
	$db = JFactory::getDbo();

	// Filter
	$search = $app->input->getString('search');

	// menu is defined
	$menu= '';
	if($app->input->getString('menu')) $menu = 'menutype = "'.$app->input->getString('menu').'" AND ';

	$data= array();

	$query = "SELECT title, alias, path, link, type FROM #__menu WHERE ".$menu."(title LIKE '%".$search."%' OR path LIKE '%".$search."%' OR params LIKE '%".str_replace('"', '', json_encode($search))."%') AND link <> '#' AND published = 1 ORDER BY title";
	//trigger_error($query);
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	$list = $db->loadObjectList();

	if($num_rows > 0) :
		foreach($list as $item) {
			$data[] = array(
				'title'	=> $item->title,
				'label'	=> str_replace($item->alias, $item->title, str_replace('/', ' / ', str_replace('home/', '', $item->path))),
				'path'	=> $item->path,
				'link'	=> $item->link,
				'type'	=> $item->type
			);
		}
	else :
		$data[] = array(
			'title'	=> '',
			'label'	=> 'Nenhum serviço foi localizado...',
			'path'	=> '',
			'link'	=> '',
			'type'	=> ''
		);
	endif;

	$json = json_encode( $data );

	# JSON if no callback
	if(!isset($_GET['callback'])) exit($json);

	# JSONP if valid callback
	if(is_valid_callback($_GET['callback'])) exit("{$_GET['callback']}($json)");

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

//endif;

?>
