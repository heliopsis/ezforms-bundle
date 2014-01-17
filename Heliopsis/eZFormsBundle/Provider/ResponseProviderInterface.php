<?php
/**
 * @author: bchoquet
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