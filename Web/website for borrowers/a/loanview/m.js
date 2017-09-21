console.log('movemoney_init');

function movemoney(mode){		// обиаем пакет еквизитов для движения денег 
	
	var sm={},mm=[
			['sofdate','samount','sopdate','soacc','snote'],	// ля отпавки
			['cashman','rofdate','ramount','ropdate','roacc','rnote'],	// ля пиема денег
		];
	
	for (r=0; r<mm[mode].length; r++) sm[mm[mode][r]] = $('#'+mm[mode][r]).val();
	
	// var cs=$('#fromacc').find("option:selected");  sm['fromacc'] =$(cs).text();  console.log(cs);
	lfon(0); $.ajax({ url: turl+bs+'cajx=movemoney&mode='+mode+'&rnd='+backRnd(),data:sm});
}

function recalc(){				// оманда на пееачет  обновлением таницы ,data:{}
	lfon(0); $.ajax({ url: turl+bs+'cajx=recalc&rnd='+backRnd()});
}

function changeStatus(mode){				// тказать в кедите  комментаием
	var sm={}; sm['dnote'] = $('#dnote').val();
	lfon(0); $.ajax({ url: turl+bs+'cajx='+mode+'&rnd='+backRnd(),data:sm});
}