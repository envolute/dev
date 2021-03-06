<?php
// SAVE
// executa a ação de inserção ou atualização dos dados no banco
?>
window.<?php echo $APPTAG?>_save = function(trigger) {
	// valida o formulário antes do envio -> 'jquery validation'
	if(mainForm.valid()) {
		<?php
		// FORMAT VALUES -> Formatação de valores para inclusão no banco
		require(JPATH_CORE.DS.'apps/snippets/form/formatValues.js.php');
		?>
		// pega os dados enviados pelo form
		var dados = <?php echo ($cfg['hasUpload'] ? 'new FormData(mainForm[0])' : 'mainForm.serialize()') ?>;
		// executando...
		<?php echo $APPTAG?>_formExecute(true, true, true); // inicia o loader

		jQuery.ajax({
			url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=save&id="+formId.val(),
			dataType: 'json',
			type: 'POST',
			method: "POST",
			data:  dados,
			cache: false,
			<?php if($cfg['hasUpload']): // quando houver upload ?>
			processData: false,
			contentType: false,
			<?php endif; ?>
			success: function(data){
				<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
				jQuery.map( data, function( res ) {
					if(res.status > 0) { // se alguma ação for realizada

						// res.status = 1 ? 'novo' : 'atualizado';
						var resID = (res.status == 1) ? res.regID : formId.val();

						if(isSet(trigger)) {
							if(trigger == 'close' || trigger == 'reset') {
								<?php echo $APPTAG?>_formReset();
								if(trigger == 'close') popup.modal('hide');
							} else if(typeof(trigger) === 'function') {
								// Caso haja o "reset", deve ser chamado na função "trigger"
								trigger(resID);
							}
						} else {
							<?php echo $APPTAG?>_loadEditFields(resID, true, false); // recarrega os dados do form
						}

						// Update Parent field
						if(res.parentField != '' && res.parentFieldVal != '') {
							jQuery(res.parentField).each(function() {
								var obj = jQuery(this);
								// remove if option exist
								if(obj.find('option[value="'+res.parentFieldVal+'"]').length) jQuery(res.parentField).find('option[value="'+res.parentFieldVal+'"]').remove();
								// add option if is active (state = 1)
								if(res.parentFieldLabel != '' && res.parentFieldLabel != null) {
									obj.append('<option value='+res.parentFieldVal+'>'+res.parentFieldLabel+'</option>'); // add valor à lista
									// Atribui o valor ao campo apenas se o mesmo estiver visível...
									// Assim, evita selecionar o campo em outras 'Apps' que não estiverem sendo editadas
									// Obs: o valor é adicionado as outras 'Apps', mas não selecionado!
									var fieldVal = obj.parent().is(':visible') ? res.parentFieldVal : 0;
									obj.selectUpdate(fieldVal); // atualiza o select
								} else {
									obj.selectUpdate();
								}
							});
						}

						<?php // SUCCESS STATUS -> Executa quando houver sucesso na requisição ajax
						require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxSuccess.js.php');
						?>
						<?php // recarrega a página quando fechar o form para atualizar a lista
						if(isset($cfg['listFull']) || isset($cfg['itemView'])) :
							if(!$cfg['listFull'] && !$cfg['itemView']) :
								echo $APPTAG.'_listReload(false, false, false, '.$APPTAG.'oCHL, '.$APPTAG.'rNID, '.$APPTAG.'rID);';
							else :
								echo '
									fReload = true;
									pReload = resID;
								';
							endif;
						endif;
						?>
						if(firstField.length) setTimeout(function() { firstField.focus() }, 10); // seta novamente o focus no primeiro campo

					} else {

						// caso ocorra um erro na ação, mostra a mensagem de erro
						$.baseNotify({ msg: res.msg, type: "danger"});

						// recarrega os scripts de formulário para os campos
						// necessário após um procedimento ajax que envolve os elementos
						setFormDefinitions();

					}
				});
			},
			error: function(xhr, status, error) {
				<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
				require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
				?>
				<?php echo $APPTAG?>_formExecute(true, true, false); // encerra o loader
			}
		});
	}
};
