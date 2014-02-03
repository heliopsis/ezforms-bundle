<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\Provider;

use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\FormInterface;
use Heliopsis\eZFormsBundle\Exceptions\UnknownFormException;

interface FormProviderInterface
{
    /**
     * Returns symfony form to display at $location
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Heliopsis\eZFormsBundle\Exceptions\UnknownFormException if no form matches $location
     */
    public function getForm( Location $location );
}
