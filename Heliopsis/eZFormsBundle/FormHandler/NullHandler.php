<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle\FormHandler;


class NullHandler implements FormHandlerInterface
{
    /**
     * Exécute le traitement
     * @param mixed $data
     * @return void
     */
    public function handle($data)
    {
        //null :)
    }

}