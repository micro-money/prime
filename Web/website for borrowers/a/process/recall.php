<?php
/*
#    
update users_calls set a_rst=0;
update users_calls set a_rst=1 where recall>now();
#     
update leads set a_rst=0;
update leads l,users_calls c set l.a_rst=1 where c.a_rst=1 and c.dt=1 and c.did=l.id;
*//**/


#    .
#       
$o=db_insert_ar("update users_calls set a_rst=0 where a_rst=1 and recall<now()");
$cron_ar=$o['a'];

if ($cron_ar==0) $cron_nolog=1;	#         

#                    a_rst=2
db_request("update leads set a_rst=2 where a_rst=1");
#    a_rst=2       
db_request("update leads l,users_calls c set l.a_rst=1 where c.a_rst=1 and c.dt=1 and c.did=l.id");