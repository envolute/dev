<?php
// Verifica se o usuário é um 'Administrador'
// GERADOR DE SENHA RANDOMICA
// seleciona todos os usuários do grupo 'desenvolvedor'
// seleciona todos os usuários do grupo 'desenvolvedor'
// seleciona todos os usuários, exceto os do grupo 'desenvolvedor'
<fieldset class="fieldset-bordered">
<?php
	$users = $_POST['users'];
	//remove o grupo 'desenvolvedor', que é fixo, mais os grupos selecionados
	$where = '';
	$db->setQuery($query);
	$countOn = $countOff = 0;
		$setPass = random_password();
		$query = "UPDATE #__users SET password='".$newPass."' WHERE id=".$obj->id;
		if($mailer->Send() && $update) :
endif;