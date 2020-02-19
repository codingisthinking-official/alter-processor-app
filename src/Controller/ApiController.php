<?php

namespace App\Controller;

use App\Entity\Deal;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController
{
    const API_URL_FILE = 'https://cms.dmn002.com/api/files/%s/';

    /**
     * @Route("/api/contact/", methods={"OPTIONS", "POST"})
     */
    public function sendContactMessage(Request $request, \Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('origin'));
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $response->headers->set('Access-Control-Allow-Methods', 'OPTIONS,GET,POST');

        if ($request->isMethod('OPTIONS')) {
            return $response;
        }

        $message = (new \Swift_Message('Wiadomość ze strony'))
            ->setFrom('kontakt@alterinvestment.pl')
            ->setTo('mateusz@codingisthinking.com')
            ->setBody(
                $templating->render(
                    'emails/contact.html.twig',
                    ['form' => json_decode($request->getContent(), true)]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $response;
    }


    /**
     * @Route("/api/deal/", methods={"OPTIONS", "POST"})
     */
    public function deal(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $httpResponse = new Response();
        $httpResponse->headers->set('Access-Control-Allow-Origin', $request->headers->get('origin'));
        $httpResponse->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $httpResponse->headers->set('Access-Control-Allow-Methods', 'OPTIONS,GET');

        if ($request->isMethod('OPTIONS')) {
            return $httpResponse;
        }

        $deal = $serializer->deserialize($request->getContent(), 'App\Entity\Deal', 'json');
        $deal->setStatusEmail(Deal::STATUS_EMAIL_NOT_SENT);

        $validatorOutput = $validator->validate($deal);

        if (count($validatorOutput) === 0) {
            $entityManager->persist($deal);
            $entityManager->flush();

            $httpResponse->setContent($request->getContent());
        } else {
            $httpResponse->setContent($validatorOutput);
        }

        return $httpResponse;
    }

    /**
     * @Route("/api/documents/{documentId}/", methods={"OPTIONS", "POST"})
     */
    public function documentSave(Request $request, int $documentId)
    {
        $httpResponse = new Response();
        $httpResponse->headers->set('Access-Control-Allow-Origin', $request->headers->get('origin'));
        $httpResponse->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $httpResponse->headers->set('Access-Control-Allow-Methods', 'OPTIONS,GET');

        if ($request->isMethod('OPTIONS')) {
            return $httpResponse;
        }

        $client = HttpClient::create();
        $response = $client->request('GET', sprintf(self::API_URL_FILE, $documentId));

        $response = $response->toArray();
        $templateProcessor = new TemplateProcessor($response['file']);

        $replaceMap = json_decode($request->getContent(), true);

        foreach ($replaceMap as $k => $v) {
            $templateProcessor->setValue($k, $v);
        }

        $hash = rand(1, 1000000) . '-' . substr(sha1(microtime(true)), 0, 4);
        $filePath = 'osw-' . $hash . '.doc';
        $templateProcessor->saveAs('docs/' . $filePath);

        $httpResponse->setContent($request->getUriForPath('/docs/') . $filePath);
        $httpResponse->setStatusCode(200);

        return $httpResponse;
    }
}
