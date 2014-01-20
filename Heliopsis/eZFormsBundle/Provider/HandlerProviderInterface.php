<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\Provider;

use eZ\Publish\API\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;

interface HandlerProviderInterface
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location );
}
