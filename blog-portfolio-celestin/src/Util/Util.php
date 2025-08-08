<?php
namespace App\Util;

use App\Entity\Utilisateur;

class Util
{
    // ... autres méthodes ...

    /**
     * Formate un montant en ajoutant le symbole euro et en utilisant un espace comme séparateur de milliers.
     *
     * @param float|null $montant Le montant à formater.
     * @return string Le montant formaté avec le symbole euro.
     */
    public static function formatMontant(?float $montant): string
    {
        if ($montant === null) {
            return '';
        }

        // Formater le montant avec deux décimales, des espaces comme séparateurs de milliers, et ajouter le symbole euro
        $formattedMontant = number_format($montant, 2, ',', ' ');
        return ucfirst($formattedMontant) . ' €';
    }

    // ... autres méthodes ...

    public static function age($dateNaissance, $anneeRef = null, $moisRef = null, $jourRef = null)
    {
        if (null === $anneeRef && null === $moisRef && null === $jourRef) {
            $dateRef = new \DateTime();
        } else {
            if (null === $anneeRef) {
                $anneeRef = date('Y');
            }

            if (null === $moisRef) {
                $moisRef = '12';
            }

            if (null === $jourRef) {
                $jourRef = '31';
            }
            $dateRef = new \DateTime($anneeRef.'-'.$moisRef.'-'.$jourRef);
        }

        return $dateNaissance->diff($dateRef)->format('%y');
    }

    public static function ageAvecVirgule($dateNaissance, $anneeRef = null, $moisRef = null, $jourRef = null)
    {
        if (null === $anneeRef && null === $moisRef && null === $jourRef) {
            $dateRef = new \DateTime();
        } else {
            if (null === $anneeRef) {
                $anneeRef = date('Y');
            }

            if (null === $moisRef) {
                $moisRef = '12';
            }

            if (null === $jourRef) {
                $jourRef = '31';
            }
            $dateRef = new \DateTime($anneeRef.'-'.$moisRef.'-'.$jourRef);
        }

        return (int)($dateNaissance->diff($dateRef)->days*100/365.25)/100;
    }

    public static function anciennete($date, $anneeRef = null, $moisRef = null, $jourRef = null)
    {
        if (null === $anneeRef) {
            $anneeRef = date('Y');
        }

        if (null === $moisRef) {
            $moisRef = '12';
        }

        if (null === $jourRef) {
            $jourRef = '31';
        }

        $dateRef = new \DateTime($anneeRef.'-'.$moisRef.'-'.$jourRef);

        $dateRef->modify('+1 day');

        return $date->diff($dateRef)->format('%y an(s), %m mois et %d jour(s)');
    }

    private static function utilisateur_identite(Utilisateur $utilisateur): string
    {
        return self::twig_title($utilisateur->getCivilite()).' '.self::twig_title($utilisateur->getPrenom()).' '.self::twig_upper($utilisateur->getNom());
    }

    private static function utilisateur_role(Utilisateur $utilisateur): string
    {
        return self::twig_title($utilisateur->getRoles());
    }

    private static function agent_identite(AgentEmployes $agent): string
    {
        return self::twig_title($agent->getCivilite()).' '.self::twig_title($agent->getPrenom()).' '.self::twig_upper($agent->getNomUsage());
    }

    private static function ligne_identite(Ligne $ligne): string
    {
        return self::twig_title($ligne->getCivilite()).' '.self::twig_title($ligne->getPrenom()).' '.self::twig_upper($ligne->getNomUsage());
    }

    public static function identite($object): string
    {
        if ($object instanceof Utilisateur) {
            return self::utilisateur_identite($object);
        }

        if ($object instanceof AgentEmployes) {
            return self::agent_identite($object);
        }

        if ($object instanceof Ligne) {
            return self::ligne_identite($object);
        }

        return '';
    }

    public static function role($object): string
    {
        if ($object instanceof Utilisateur) {
            return self::utilisateur_role($object);
        }

        return '';
    }

    public static function twig_date($dateTime, $format = 'd/m/Y'): string
    {
        if ($dateTime instanceof \DateTime) {
            return $dateTime->format($format);
        }

        if (is_int($dateTime)) {
            return date($format, $dateTime);
        }

        return '';
    }

    public static function twig_dateTime($dateTime, $format = 'd/m/Y H:i:s'): string
    {
        if ($dateTime instanceof \DateTime) {
            return $dateTime->format($format);
        }

        if (is_int($dateTime)) {
            return date($format, $dateTime);
        }

        return '';
    }

    public static function twig_capitalize($string)
    {
        if (null === $string) {
            return '';
        }

        $charset = 'UTF-8';

        return mb_strtoupper(mb_substr($string, 0, 1, $charset), $charset).mb_strtolower(mb_substr($string, 1, mb_strlen($string, $charset), $charset), $charset);
    }

    public static function twig_upper($string)
    {
        if (null === $string) {
            return '';
        }

        $charset = 'UTF-8';

        return mb_strtoupper($string, $charset);
    }

    public static function twig_lower($string)
    {
        if (null === $string) {
            return '';
        }

        $charset = 'UTF-8';

        return mb_strtolower($string, $charset);
    }

    public static function twig_title($string)
    {
        if (null === $string) {
            return '';
        }

        $charset = 'UTF-8';

        return mb_convert_case($string, MB_CASE_TITLE, $charset);
    }

    public static function str_starts_with($haystack, $needle): bool
    {
        return '' !== (string) $needle && 0 === strncmp($haystack, $needle, strlen($needle));
    }

    public static function str_ends_with($haystack, $needle)
    {
        return '' !== $needle && substr($haystack, -strlen($needle)) === (string) $needle;
    }

    public static function str_contains($haystack, $needle)
    {
        return '' !== $needle && false !== mb_strpos($haystack, $needle);
    }

    public static function ouiNon($bool): string
    {
        $result = '';

        if (true === $bool) {
            $result = 'oui';
        } elseif (false === $bool) {
            $result = 'non';
        }

        return $result;
    }

    public static function formatDate($date, $format = 'd/m/Y'): string
    {
        if ($date instanceof \DateTime) {
            return date($format, $date->getTimestamp());
        }

        return '';
    }

    public static function formaterInt($int): int
    {
        // S'assurer que l'argument est bien un entier
        $formattedInt = (int)$int;

        // Retourner l'entier formaté
        return $formattedInt;
    }

}
