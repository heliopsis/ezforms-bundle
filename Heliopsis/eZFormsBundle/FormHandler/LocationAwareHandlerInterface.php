<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
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
