<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LocaleController extends AbstractController
{
    #[Route('/switch-locale/{lang}', name: 'switch_language')]
    public function switchLocale(string $lang, Request $request): Response
    {
        $allowedLocales = ['hr', 'en']; // A whitelist as I have seen advice to noy trust raw input

        if (!in_array($lang, $allowedLocales)) {
            $lang = 'en';
        } //If the language is incorrect we default to english

        $request->getSession()->set('language', $lang);

        return $this->redirect($request->headers->get('referer','/'));
    }
}
