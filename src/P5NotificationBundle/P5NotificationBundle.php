<?php

namespace P5NotificationBundle;

use P5NotificationBundle\DependencyInjection\P5NotificationExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class P5NotificationBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new P5NotificationExtension();
    }
}
