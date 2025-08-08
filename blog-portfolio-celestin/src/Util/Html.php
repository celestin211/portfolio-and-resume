<?php

declare(strict_types=1);

namespace App\Util;

final class Html
{
    /**
     * @var string
     */
    private $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Retourne si la chaîne de caractères contient une balise non présente dans celles autorisées.
     */
    public function contientAutreBaliseHtml(array $balisesAutorisees = []): bool
    {
        // Si aucune balise autorisée, on retourne si la chaine en contient une
        if (empty($balisesAutorisees)) {
            return Chaine::contientBaliseHtml($this->html);
        }

        return $this->html !== strip_tags($this->html, '<'.implode('><', $balisesAutorisees).'>') || str_contains($this->html, 'javascript:');
    }

    public function contientLienNeCommencantPasPar(array $debutsLiensAutorises): bool
    {
        $resultats = [];
        // On récupère l'intérieur de toutes les balises <a>
        preg_match_all("/<\s*a([^>]*)>(.*?)<\s*\/\s*a\s*>/i", $this->html, $resultats);

        // Pour chaque balise <a>
        foreach ($resultats[1] as $baliseInterieur) {
            $hrefResultats = [];
            // On récupère ce qu'il y a après href=
            preg_match_all("/\s*href\s*=\s*(.*)/i", $baliseInterieur, $hrefResultats);
            $debutLien = ltrim($hrefResultats[1][0], ' "');

            $debutValide = false;

            foreach ($debutsLiensAutorises as $debutLienAutorise) {
                // Si le début du href est valide
                if ($debutLienAutorise === mb_substr($debutLien, 0, mb_strlen($debutLienAutorise))) {
                    $debutValide = true;

                    break;
                }
            }

            if (!$debutValide) {
                return false;
            }
        }

        return true;
    }

    public function contientAttributNonAutorise(array $attributsAutorises): bool
    {
        // Récupère les attributs
        $balisesAvecAttributs = $this->getBalisesAvecAttributs();

        foreach ($balisesAvecAttributs as $balise => $attributs) {
            if (empty($attributs)) {
                continue;
            }

            $baliseAttributsAutorises = array_merge($attributsAutorises[''], isset($attributsAutorises[$balise]) ? $attributsAutorises[$balise] : []);

            foreach ($attributs as $attribut) {
                if (!\in_array($attribut, $baliseAttributsAutorises, true)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function getBalisesAvecAttributs(): array
    {
        $balisesAvecAttributs = [];
        $regexBalises = [];
        // On récupère toutes les balises HTML (par exemple `<a href="http://toto.fr/">Toto</a>`)
        preg_match_all("/<\s*([^>]*)>(.*?)<\s*\/\s*([^>]*)\s*>/i", $this->html, $regexBalises);

        foreach ($regexBalises[1] as $i => $regexBalise) {
            $baliseContenu = $regexBalises[0][$i];
            $attributs = [];
            // Pour chaque balise HTML, on récupère le nom de la balise et tous ses attributs
            preg_match_all("/(\S+)=[\"']?((?:.(?![\"']?\s+(?:\S+)=|\s*\/?[>\"']))+.)[\"']?/i", $baliseContenu, $attributs);

            $baliseNom = $regexBalises[3][$i];
            $balisesAvecAttributs[$baliseNom] = array_unique(array_merge(isset($balisesAvecAttributs[$baliseNom]) ? $balisesAvecAttributs[$baliseNom] : [], $attributs[1]));
        }

        return $balisesAvecAttributs;
    }
}
