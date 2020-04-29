<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Sign;
use Contao\ImagineSvg\Imagine;
use Imagine\Image\Box;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SignController extends AbstractController
{
    const DEFAULT_WIDTH = 25;
    const DEFAULT_HEIGHT= 25;

    protected string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @Route("/sign/{sign_number}", name="sign")
     * @ParamConverter("sign", class="App:Sign", options={"mapping": {"sign_number": "number"}})
     */
    public function sign(Request $request, Sign $sign): Response
    {
        $width = $request->get('width', self::DEFAULT_WIDTH);
        $height = $request->get('height', self::DEFAULT_HEIGHT);

        $filename = sprintf('%s/public/images/sign/%s', $this->projectDir, $sign->getImageName());
        $imagine = new Imagine();

        $imageContent = $imagine
            ->open($filename)
            ->resize(new Box($width, $height))
            ->get('svg');

        return new Response($imageContent, 200, [
            'Content-type' => 'image/svg+xml',
        ]);
    }
}
