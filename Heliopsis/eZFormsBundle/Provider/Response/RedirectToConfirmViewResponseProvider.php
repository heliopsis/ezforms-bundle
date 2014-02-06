<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Provider\Response;

use eZ\Publish\API\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\Provider\ResponseProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectToConfirmViewResponseProvider implements ResponseProviderInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private $confirmViewType;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param string $confirmViewType
     */
    function __construct(UrlGeneratorInterface $urlGenerator, $confirmViewType )
    {
        $this->urlGenerator = $urlGenerator;
        $this->confirmViewType = $confirmViewType;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param mixed $data
     * @return Response
     */
    public function getResponse( Location $location, $data )
    {
        return new RedirectResponse(
            $this->urlGenerator->generate(
                '_ezpublishLocation',
                array(
                    'locationId' => $location->id,
                    'viewType' => $this->confirmViewType,
                )
            )
        );
    }
}
