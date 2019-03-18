<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Sign;
use Contao\ImagineSvg\Imagine;
use Imagine\Image\Box;
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
    public function sign(Sign $sign): Response
    {
        $filename = sprintf('/application/public/images/sign/%s', $sign->getImageName());
        $imagine = new Imagine();

        $imageContent = $imagine
            ->open($filename)
            ->resize(new Box(40, 40))
            ->get('svg');

        return new Response($imageContent, 200, [
            'Content-type' => 'image/svg+xml',
        ]);
    }
}
