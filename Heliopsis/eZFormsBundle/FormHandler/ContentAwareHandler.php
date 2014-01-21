<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

use eZ\Publish\API\Repository\Values\Content\Content;

abstract class ContentAwareHandler implements ContentAwareHandlerInterface
{
    /**
     * @var Content
     */
    private $content;

    /**
     * @param Content $content
     * @return void
     */
    public function setContent( Content $content )
    {
        $this->content = $content;
    }

    /**
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }
}
