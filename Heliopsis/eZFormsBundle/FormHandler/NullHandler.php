<?php
/**
 * Class NullHandler
 *
 * If you ever don't really want to handle data...
 *
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

class NullHandler implements FormHandlerInterface
{
    /**
     * Does nothing
     * @param mixed $data
     * @return void
     */
    public function handle($data)
    {
        //null :)
    }

}
