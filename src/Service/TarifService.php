<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

/*
Exercice 1 - Paramètres et configuration

Créez un service TarifService qui calcule des prix TTC à partir de prix HT.

Exigences :

    Le taux de TVA est configurable via un paramètre app.tva_rate dans services.yaml
    Injectez ce paramètre via #[Autowire]
    Exposez les méthodes :
        htToTtc(float $ht): float
        ttcToHt(float $ttc): float
        getTauxTva(): float
    Changez le taux dans services.yaml et vérifiez que le service s’adapte sans toucher au code PHP


*/

class TarifService
{
    public function __construct(
        #[Autowire('%app.tva_rate%')]
        private float $tvaRate
    ) {}

    public function htToTtc(float $ht): float
    {
        return $ht * (1 + $this->tvaRate);
    }

    public function ttcToHt(float $ttc): float
    {
        return $ttc / (1 + $this->tvaRate);
    }

    public function getTauxTva(): float
    {
        return $this->tvaRate;
    }
}