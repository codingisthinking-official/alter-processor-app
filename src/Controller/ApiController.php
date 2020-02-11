<?php

namespace App\Controller;

use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

class ApiController
{
    const API_URL_FILE = 'https://cms.dmn002.com/api/files/%s/';

    /**
     * @Route("/api/documents/{documentId}/", methods={"POST"})
     */
    public function number(Request $request, int $documentId)
    {
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

        return new Response($request->getUriForPath('/docs/') . $filePath, 200);
    }
}