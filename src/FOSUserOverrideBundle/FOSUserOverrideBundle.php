<?php

namespace FOSUserOverrideBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FOSUserOverrideBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
