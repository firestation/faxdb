<?php

$start = filemtime('/etc/faxweb.ini');

while(true) {
usleep(1000000);
clearstatcache('/etc/faxweb.ini');
if ($start != filemtime('/etc/faxweb.ini'))
    echo "Changed!";

echo "$start.\n";

}

?>