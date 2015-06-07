<?php
t('You have not published your Conexting wall {name}.',array('{name}'=>'"'.$wall->name.'"'));
echo ' ';
t('The wall will be removed on {date} if it is not published or updated before that date.',array('{date}'=>date('j.n.Y',$wall->dies)));
