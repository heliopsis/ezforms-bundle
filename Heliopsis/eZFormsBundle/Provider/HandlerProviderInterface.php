<?php
/**
 * @author: bchoquet
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