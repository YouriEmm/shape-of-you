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
        "Je souhaite avoir dans la partie 'nom' le type de vêtement suivi de la spécificité du vêtement s'il y en a (par exemple cargo pour un pantalon) et de la couleur.\n" .
        "Essaie de trouver la marque pour chaque vêtement et fais tout ton possible pour fournir une marque.\n" .
        "Voici un exemple de la structure attendue :\n" .
        "{\n" .
        "   'Vêtement 1': { 'Nom': 'T-shirt Noir Col Rond', 'categorie': 't-shirt', 'Marque': 'Nike' },\n" .
        "   'Vêtement 2': { 'Nom': 'Jean Slim Bleu Indigo','categorie': 'jean', 'Marque': 'Levi\'s' },\n" .
        "   'Vêtement 3': { 'Nom': 'Sneakers Blanches', 'categorie': 'chaussures', 'Marque': 'Adidas' },\n" .
        "   'Vêtement 4': { 'Nom': 'Blouson en Cuir Marron', 'categorie': 'blouson', 'Marque': 'Zara' },\n" .
        "   'Vêtement 5': { 'Nom': 'Sac à Main Noir', 'categorie': 'accessoire', 'Marque': 'Chanel' }\n" .
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

    public function generateOutfit(string $description, array $wardrobeItems)
    {
        $itemsList = implode(", ", array_map(function($item) {
            return "{$item->getName()} (ID: {$item->getId()})";
        }, $wardrobeItems));
            
        $prompt = "Tu es un styliste de mode. À partir de la demande/description suivante : '$description', crée un outfit en utilisant uniquement les vêtements suivants : $itemsList. 
        Retourne seulement un JSON avec exactement ce format :
        {
            'outfit_name': 'Nom de l\'outfit',
            'items': [
                { 'name': 'Nom du vêtement', 'id': 'ID du vêtement' },
                { 'name': 'Autre vêtement', 'id': 'ID du vêtement' }
            ]
        }
        N'inclus aucune autre information ou commentaire en dehors du JSON.";
    
        $messages = [
            [
                "role" => "user",
                "content" => $prompt,
            ],
        ];
    
        try {
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
    
            if (isset($data['choices'][0]['message']['content'])) {
                $generatedOutfit = $data['choices'][0]['message']['content'];
                return $generatedOutfit;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return [
                'outfit' => null,
                'status' => 'error',
                'message' => 'Erreur lors de l\'appel à l\'API : ' . $e->getMessage(),
            ];
        }
    }
    

}
