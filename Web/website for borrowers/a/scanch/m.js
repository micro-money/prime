console.log('mjs init');

function nextch(mp) {
	// Ищем все не отмеченные input под el элементом собираем из них массив и отправляем на сервер с текущим заданием и текущей установкой для выдачи след пакета сканов
	var el=mp.el,wd={'sas_el':el,'wtype':mp.wtype,'idl':[]}; wd[el+'(setd)']=mp.wtype;
	$('#'+el+' input:checkbox:not(:checked)').each(function(){
		wd['idl'][wd['idl'].length]=$(this).attr('rowid');
	});
	lfon(0); $.ajax({ url: turl+bs+'sas_init=refel&rnd='+backRnd(),data:wd });
}