
function seekaio(mp){				// нимаем данные для аботы
	var el=mp.el,pd={'aio_el':el};
	if ($('#'+el+'_aios').is('input')) pd['aios']=$('#'+el+'_aios')[0].value;
	if ($('#'+el+'_aiom').is('select')) pd['aiom']=$('#'+el+'_aiom')[0].value;
	lfon(0); $.ajax({ url: turl+bs+'cajx=seekaio&rnd='+backRnd(),data:pd });
}

function addAioPar(mp) {			// обавляем паамет в поик
	var el=mp.aio_el,wf=mp.wf; 
	
	if ($('#'+el+'_aioq').is('div')) $('#'+el+'_aioq')[0].remove();		// даляем элемент ели он уже еть , и заменяем его новым
	
	var rtmpl=$('#'+el+'_hdiv').find('[stmpl = "colwhere"]');				// щем в md_hdiv нужный шаблон
	var inpz=$('#'+el+'_showhere');
	$(rtmpl[0]).clone().appendTo($(inpz));				// убликуем
	var wtmpl=$(inpz).find('[stmpl = "colwhere"]');		// ашли опубликованный элемент
	var ohtml=$(wtmpl).html(); var nhtml=ReplaceAlls(ohtml,'WHEREVALUE',wf);	// одменили шаблон на новое значение
	$(wtmpl).html(nhtml);								// ееопубликовали новое значение
	$(wtmpl).removeAttr("stmpl");						// бязательно убиваем атибут шаблона
	$(wtmpl).attr('wn',mp.wn);$(wtmpl).attr('wv',mp.wv);$(wtmpl).attr('ws',mp.ws); // танавливаем атибуты wn wv ws
	$(wtmpl).attr('wf',wf);
	$(wtmpl).attr('id',el+'_aioq');

	sas_seek({'el':el});
	
}