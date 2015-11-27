<?php

namespace P4M\MyElasticaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class P4MMyElasticaBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSElasticaBundle';
    }
}
