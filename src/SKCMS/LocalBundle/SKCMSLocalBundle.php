<?php

namespace SKCMS\LocalBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SKCMSLocalBundle extends Bundle{
    public function getParent(){
        return 'SKCMSLocaleBundle';
    }
}
