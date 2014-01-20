<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

use eZ\Publish\API\Repository\Values\Content\Content;

interface ContentAwareHandlerInterface extends FormHandlerInterface
{
    /**
     * @param Content $content
     * @return void
     */
    public function setContent( Content $content );
}
