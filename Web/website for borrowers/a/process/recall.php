<?php
/*
# Реиниц перезвона всем активным
update users_calls set a_rst=0;
update users_calls set a_rst=1 where recall>now();
# Реиниц перезвона всем активным лидам
update leads set a_rst=0;
update leads l,users_calls c set l.a_rst=1 where c.a_rst=1 and c.dt=1 and c.did=l.id;
*//**/


# Ежедневный сброс перезвона лидам.
# Сбрасываем перезвон звонкам у кого он наступил
$o=db_insert_ar("update users_calls set a_rst=0 where a_rst=1 and recall<now()");
$cron_ar=$o['a'];

if ($cron_ar==0) $cron_nolog=1;	# Не пишем в лог если не наступило время перезвонить

# На основании старых статусов на перезвон и отсутвии таковых у текущих звонков помечаем лиды которым статус перезвона сбросился как a_rst=2
db_request("update leads set a_rst=2 where a_rst=1");
# Возвращаем все те a_rst=2 у кого еще висит статус на перезвон
db_request("update leads l,users_calls c set l.a_rst=1 where c.a_rst=1 and c.dt=1 and c.did=l.id");