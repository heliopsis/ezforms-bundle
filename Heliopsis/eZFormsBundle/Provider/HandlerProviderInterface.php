<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle\Provider;


use eZ\Publish\API\Repository\Values\Content\Location;

interface HandlerProviderInterface
{
    /**
     * @param Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location );
} 