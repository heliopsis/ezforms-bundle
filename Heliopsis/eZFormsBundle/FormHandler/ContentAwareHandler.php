<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
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
