<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle\Provider;


use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\FormInterface;
use Heliopsis\eZFormsBundle\Exceptions\UnknownFormException;

interface FormProviderInterface
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Heliopsis\eZFormsBundle\Exceptions\UnknownFormException si aucun formulaire ne correspond
     */
    public function getForm( Location $location );
}