
function seekaio(mp){				// Снимаем данные для работы
	var el=mp.el,pd={'aio_el':el};
	if ($('#'+el+'_aios').is('input')) pd['aios']=$('#'+el+'_aios')[0].value;
	if ($('#'+el+'_aiom').is('select')) pd['aiom']=$('#'+el+'_aiom')[0].value;
	lfon(0); $.ajax({ url: turl+bs+'cajx=seekaio&rnd='+backRnd(),data:pd });
}

function addAioPar(mp) {			// Добавляем параметр в поиск
	var el=mp.aio_el,wf=mp.wf; 
	
	if ($('#'+el+'_aioq').is('div')) $('#'+el+'_aioq')[0].remove();		// Удаляем элемент если он уже есть , и заменяем его новым
	
	var rtmpl=$('#'+el+'_hdiv').find('[stmpl = "colwhere"]');				// Ищем в md_hdiv нужный шаблон
	var inpz=$('#'+el+'_showhere');
	$(rtmpl[0]).clone().appendTo($(inpz));				// Публикуем
	var wtmpl=$(inpz).find('[stmpl = "colwhere"]');		// Нашли опубликованный элемент
	var ohtml=$(wtmpl).html(); var nhtml=ReplaceAlls(ohtml,'WHEREVALUE',wf);	// Подменили шаблон на новое значение
	$(wtmpl).html(nhtml);								// Переопубликовали новое значение
	$(wtmpl).removeAttr("stmpl");						// Обязательно убиваем атрибут шаблона
	$(wtmpl).attr('wn',mp.wn);$(wtmpl).attr('wv',mp.wv);$(wtmpl).attr('ws',mp.ws); // Устанавливаем атрибуты wn wv ws
	$(wtmpl).attr('wf',wf);
	$(wtmpl).attr('id',el+'_aioq');

	sas_seek({'el':el});
	
}