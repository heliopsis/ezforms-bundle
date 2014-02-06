<?php
/**
 * Class NullHandler
 *
 * If you ever don't really want to handle data...
 *
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
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
