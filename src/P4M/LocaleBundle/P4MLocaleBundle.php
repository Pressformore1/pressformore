<?php

namespace P4M\LocaleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LocaleBundle extends Bundle{
    public function getParent(){
        return 'SKCMSLocaleBundle';
    }
}
