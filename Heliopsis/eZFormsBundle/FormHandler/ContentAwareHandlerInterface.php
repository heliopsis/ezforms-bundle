<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

use eZ\Publish\API\Repository\Values\Content\Content;

interface ContentAwareHandlerInterface extends FormHandlerInterface
{
    /**
     * @param Content $content
     * @return $this
     */
    public function setContent( Content $content );
}