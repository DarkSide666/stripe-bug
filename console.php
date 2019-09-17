<?php
require 'init.php';

try{
    eval(\Psy\sh());
}catch(\atk4\core\Exception $e){
    echo $e->getColorfulText();
    exit(1);
}
