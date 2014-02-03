<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

use eZ\Publish\API\Repository\Values\Content\Location;

abstract class LocationAwareHandler implements LocationAwareHandlerInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    private $location;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return void
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}
