<?php
/**
 * @author: bchoquet
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