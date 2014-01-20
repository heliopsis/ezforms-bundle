<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

interface FormHandlerInterface
{
    /**
     * Exécute le traitement
     * @param mixed $data
     * @return void
     */
    public function handle( $data );

}
