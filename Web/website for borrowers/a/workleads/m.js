/**/console.log('custom_js_init');

function fastbutton(cst){	
	
	var cel=$("[cajx_lid]")[0];
	var lid=$(cel).attr('cajx_lid');	
	var nct=document.getElementById("NextCallTime").value; 
	var note=document.getElementById("note").value;	
	console.log(note);
	lfon(0); $.ajax({ url: turl+bs+'cajx=next&rnd='+backRnd(),data:{'note':note,'lid':lid,'cst':cst,'nct':nct} });		
}

function chNextCall(el){
	
	var tv=el.value; var but=document.getElementById("NextCallBut");  
	var d= 'inline-block';  if (tv==0) d= 'none'; 
	but.style.display = d; 
}
