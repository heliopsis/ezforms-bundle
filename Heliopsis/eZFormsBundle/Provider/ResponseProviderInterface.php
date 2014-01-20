<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\Provider;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\HttpFoundation\Response;

interface ResponseProviderInterface
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param mixed $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(Location $location, $data);
}
