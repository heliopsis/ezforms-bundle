<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

use eZ\Publish\API\Repository\Values\Content\Location;

interface LocationAwareHandlerInterface extends FormHandlerInterface
{
    /**
     * @param Location $location
     * @return void
     */
    public function setLocation( Location $location );
}
