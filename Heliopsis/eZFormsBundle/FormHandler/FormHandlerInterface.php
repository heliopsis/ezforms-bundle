<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

interface FormHandlerInterface
{
    /**
     * @param mixed $data
     * @return void
     */
    public function handle( $data );

}
