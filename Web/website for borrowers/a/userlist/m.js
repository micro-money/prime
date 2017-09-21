
function seekaio(mp){				
	var el=mp.el,pd={'aio_el':el};
	if ($('#'+el+'_aios').is('input')) pd['aios']=$('#'+el+'_aios')[0].value;
	if ($('#'+el+'_aiom').is('select')) pd['aiom']=$('#'+el+'_aiom')[0].value;
	lfon(0); $.ajax({ url: turl+bs+'cajx=seekaio&rnd='+backRnd(),data:pd });
}

function addAioPar(mp) {			
	var el=mp.aio_el,wf=mp.wf; 
	
	if ($('#'+el+'_aioq').is('div')) $('#'+el+'_aioq')[0].remove();		
	
	var rtmpl=$('#'+el+'_hdiv').find('[stmpl = "colwhere"]');				
	var inpz=$('#'+el+'_showhere');
	$(rtmpl[0]).clone().appendTo($(inpz));				
	var wtmpl=$(inpz).find('[stmpl = "colwhere"]');		
	var ohtml=$(wtmpl).html(); var nhtml=ReplaceAlls(ohtml,'WHEREVALUE',wf);	
	$(wtmpl).html(nhtml);								
	$(wtmpl).removeAttr("stmpl");						
	$(wtmpl).attr('wn',mp.wn);$(wtmpl).attr('wv',mp.wv);$(wtmpl).attr('ws',mp.ws); 
	$(wtmpl).attr('wf',wf);
	$(wtmpl).attr('id',el+'_aioq');

	sas_seek({'el':el});
	
}