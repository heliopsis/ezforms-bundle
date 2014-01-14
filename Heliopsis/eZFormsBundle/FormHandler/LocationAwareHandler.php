<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle\FormHandler;


use eZ\Publish\API\Repository\Values\Content\Location;

abstract class LocationAwareHandler implements LocationAwareHandlerInterface
{
    /**
     * @var Location
     */
    private $location;

    /**
     * @param Location $location
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