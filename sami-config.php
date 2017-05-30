<?php

$files = new ArrayObject([
    'Arc2JsonLdConverter.php',
    'Parser.php'
]);

return new Sami\Sami($files->getIterator());
