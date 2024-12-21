<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FlashMessageHelper implements FlashMessageHelperInterface
{

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function addFormErrorsAsFlash(FormInterface $form): void
    {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $errors = $form->getErrors(true);
        //Ajouts des erreurs du formulaire comme messages flash de la catégorie "error".
        foreach ($errors as $error) {
            $errorMsg = $error->getMessage();
            $flashBag->add('error', $errorMsg);
        }
    }

    public function addFormSuccessAsFlash($message) : void {
        $flashBag = $this->requestStack->getSession()->getFlashBag();
        $flashBag->add('success', $message);
    }
}