<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

/*
Exercice 2 - Décorateur avec cache

Vous avez un service MeteoService qui appelle une API externe (coûteuse en temps et en argent).

    Créez MeteoService avec la méthode getPrevisions(string $ville): array (simuler avec des données statiques)

    Créez CachedMeteoService qui décore MeteoService :
        Cache les prévisions pendant 30 minutes par ville
        Logge via LoggerInterface si la donnée vient du cache ou de l’API
        Expose une méthode invalidateCache(string $ville): void

    Configurez le décorateur dans services.yaml

    Testez que à l’aide d’une commande le service MeteoService et regardez les logs.
    Le premier appel doit afficher “API” dans les logs, le second doit afficher “Cache”.

Bonus : ajoutez un paramètre app.meteo_cache_ttl pour rendre la durée de cache configurable.
*/

// A CORRIGER
class MeteoService
{
    public function __construct(
        #[Autowire('%env(METEO_API_KEY)%')]
        string $apiKey
    ) {
    }

    public function getPrevisions(string $ville): array
    {
        // Simuler une requête à une API météo
        return [
            'ville' => $ville,
            'temperature' => rand(-10, 35),
            'condition' => ['Ensoleillé', 'Pluvieux', 'Nuageux'][rand(0, 2)],
        ];
    }
}