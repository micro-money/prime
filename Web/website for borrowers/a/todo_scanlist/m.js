console.log('mjs init');

function nextch(mp) {
	//     input  el                    
	var el=mp.el,wd={'sas_el':el,'wtype':mp.wtype,'idm':[]}; wd[el+'(setd)']=mp.wtype;
	$('#'+el+' input:checkbox:not(:checked)').each(function(){
		wd['idm'][wd['idm'].length]=$(this).attr('rowid');
	});
	lfon(0); $.ajax({ url: turl+bs+'sas_init=refel&rnd='+backRnd(),data:wd });
}