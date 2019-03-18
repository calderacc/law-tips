<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Sign;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SignController extends AbstractController
{
    /**
     * @Route("/sign/{sign_number}", name="sign")
     * @ParamConverter("sign", class="App:Sign", options={"mapping": {"sign_number": "number"}})
     */
    public function sign(CacheManager $cacheManager, Sign $sign): Response
    {
        $imagePath = sprintf('/images/sign/%s', $sign->getImageName());
        $path = $cacheManager->getBrowserPath($imagePath, 'squared_thumbnail');

        dump($path);die;
        return $this->render('sign/index.html.twig', [
            'controller_name' => 'SignController',
        ]);
    }
}
