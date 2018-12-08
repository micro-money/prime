console.log('movemoney_init');

function movemoney(mode){		// Собираем пакет реквизитов для движения денег 
	
	var sm={},mm=[
			['sofdate','samount','sopdate','soacc','snote'],	// Для отправки
			['cashman','rofdate','ramount','ropdate','roacc','rnote'],	// Для приема денег
		];
	
	for (r=0; r<mm[mode].length; r++) sm[mm[mode][r]] = $('#'+mm[mode][r]).val();
	
	// var cs=$('#fromacc').find("option:selected");  sm['fromacc'] =$(cs).text();  console.log(cs);
	lfon(0); $.ajax({ url: turl+bs+'cajx=movemoney&mode='+mode+'&rnd='+backRnd(),data:sm});
}

function recalc(){				// Команда на перерасчет с обновлением страницы ,data:{}
	lfon(0); $.ajax({ url: turl+bs+'cajx=recalc&rnd='+backRnd()});
}

function changeStatus(mode){				// Отказать в кредите с комментарием
	var sm={}; sm['dnote'] = $('#dnote').val();
	lfon(0); $.ajax({ url: turl+bs+'cajx='+mode+'&rnd='+backRnd(),data:sm});
}