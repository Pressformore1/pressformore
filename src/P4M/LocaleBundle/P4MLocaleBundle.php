<?php

namespace P4M\LocaleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class P4MLocaleBundle extends Bundle{
    public function getParent(){
        return 'SKCMSLocaleBundle';
    }
}
