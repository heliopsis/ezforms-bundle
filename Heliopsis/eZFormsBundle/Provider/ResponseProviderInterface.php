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
     * @param Location $location
     * @parma mixed $data
     * @return Response
     */
    public function getResponse(Location $location, $data);
}