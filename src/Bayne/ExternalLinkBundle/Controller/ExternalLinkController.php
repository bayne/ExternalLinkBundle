<?php

namespace Bayne\ExternalLinkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ExternalLinkController extends Controller
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function externalLinkRedirectAction(Request $request)
    {
        return $this->redirect($request->get('_url'));
    }

}
