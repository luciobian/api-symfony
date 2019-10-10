<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadImageAction
{

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ValidatorInterface */
    private $validator;
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    )
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }
    public function __invoke(Request $request)
    {
        $image = new Image();

        $form = $this->formFactory->create(ImageType::class,$image);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($image);
            $this->entityManager->flush();
            $image->setFile(null);
            return $image;
        }

        throw new ValidationException(
            $this->validator->validate($image)
        );

    }
}