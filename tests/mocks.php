<?php

namespace PradoDigital\StatHat;

function fsockopen($host, $port)
{
    return fopen(__DIR__.'/socket_mock_contents.txt', 'w+');
}
