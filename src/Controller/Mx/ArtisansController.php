<?php

declare(strict_types=1);

namespace App\Controller\Mx;

use App\Entity\Artisan;
use App\Form\ArtisanType;
use App\Service\EnvironmentsService;
use App\Utils\Artisan\Utils;
use App\Utils\StrUtils;
use App\ValueObject\Routing\RouteName;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/mx/artisans')]
class ArtisansController extends AbstractFormController
{
    #[Route(path: '/{id}/edit', name: RouteName::MX_ARTISAN_EDIT, methods: ['GET', 'POST'])]
    #[Route(path: '/new', name: RouteName::MX_ARTISAN_NEW, methods: ['GET', 'POST'])]
    #[Cache(maxage: 0, public: false)]
    public function edit(Request $request, ?Artisan $artisan, EnvironmentsService $environments): Response
    {
        if (!$environments->isDevOrTest()) {
            throw $this->createAccessDeniedException();
        }

        $artisan ??= new Artisan();

        $form = $this->createForm(ArtisanType::class, $artisan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $artisan->getId() && self::clicked($form, ArtisanType::BTN_DELETE)) {
                $this->getDoctrine()->getManager()->remove($artisan);
            } else {
                Utils::updateContact($artisan, $artisan->getContactInfoOriginal());
                StrUtils::fixNewlines($artisan);

                $this->getDoctrine()->getManager()->persist($artisan);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(RouteName::MAIN);
        }

        return $this->render('mx/artisans/edit.html.twig', [
            'artisan' => $artisan,
            'form'    => $form->createView(),
        ]);
    }
}
