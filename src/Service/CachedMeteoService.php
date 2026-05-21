<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;

class CachedMeteoService extends MeteoService
{
    // Injection du cache
    public function __construct(
        private MeteoService $inner,
        private CacheInterface $cache,
        private LoggerInterface $logger,
        #[Autowire('%app.meteo_cache_ttl%')]
        string $meteo_cache_ttl
    ) {}
    
    // Récupération / mise en cache d'une valeur
    public function getPrevisions(string $ville): array
    {
        return $this->cache->get('meteo_' . $ville, function (ItemInterface $item) use ($ville) {
            $item->expiresAfter($meteo_cache_ttl); // 30 minutes
            
            // Appel à l'API via le service décoré
            $this->logger->info('Données venant de l\'API', ['ville' => $ville]);
            return $this->inner->getPrevisions($ville);
        });

        // Si on n'entre pas dans le callback = données du cache
        $this->logger->info('Données venant du Cache', ['ville' => $ville]);
    }

    // Suppression du cache
    public function invalidateCache(string $ville): void
    {
        $this->cache->delete('meteo_' . $ville);
    }
}