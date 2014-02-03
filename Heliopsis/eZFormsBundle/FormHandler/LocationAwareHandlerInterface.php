<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

use eZ\Publish\API\Repository\Values\Content\Location;

interface LocationAwareHandlerInterface extends FormHandlerInterface
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return void
     */
    public function setLocation( Location $location );
}
