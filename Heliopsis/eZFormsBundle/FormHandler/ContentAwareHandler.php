<?php
/**
 * @author: bchoquet
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
    protected function getContent()
    {
        return $this->content;
    }
}
