<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIService
{
    private HttpClientInterface $httpClient;
    private string $openAIKey;

    public function __construct(HttpClientInterface $httpClient, string $openAIKey)
    {
        $this->httpClient = $httpClient;
        $this->openAIKey = $openAIKey;
    }


    public function detectClothingWithVision(string $imageBase64): array|string
    {
        $imageDataUrl = "data:image/jpeg;base64," . $imageBase64;

        $prompt = "Analyse minutieusement l'image fournie.\n" .
            "Identifie chaque vêtement présent et retourne uniquement une réponse respectant exactement la structure suivante :\n" .
            "Je souhaite avoir dans la partie 'nom' le type de vêtement puis la spécificité du vêtement s'il y en a (par exemple cargo pour un pantalon) puis la couleur.\n" .
            "Essaie de trouver la marque pour chaque vêtement :\n" .
            "{\n" .
            "   'Vêtement 1': { 'Nom': 'T-shirt Blanc Casse', 'categorie': 't-shirt', 'Marque': 'Zara' },\n" .
            "   'Vêtement 2': { 'Nom': 'Pantalon Cargo Vert Kaki','categorie': 'pantalon', 'Marque': 'H&M' }\n" .
            "}\n" .
            "N'inclus aucune autre information ou commentaire en dehors du JSON.\n" .
            "Voici l'image à analyser :";


        $messages = [
            [
                "role" => "user",
                "content" => [
                    [
                        "type" => "text",
                        "text" => $prompt,
                    ],
                    [
                        "type" => "image_url",
                        "image_url" => [
                            "url" => $imageDataUrl,
                            "detail" => "high",
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->openAIKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
                'max_tokens' => 800,
            ],
        ]);

        $data = $response->toArray();
        return $data['choices'][0]['message']['content'] ?? [];
    }   
}
