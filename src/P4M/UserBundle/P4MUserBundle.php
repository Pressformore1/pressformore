<?php

namespace P4M\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class P4MUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
