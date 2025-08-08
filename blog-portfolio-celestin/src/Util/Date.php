<?php

declare(strict_types=1);

namespace App\Util;

class Date
{
    /**
     * Le format utilisé standard utilisé dans l'application.
     *
     * @todo A utiliser partout
     */
    public const FORMAT = 'd/m/Y';

    public const FORMAT_HORAIRE = 'd/m/Y, H:i:s';

    public const NOMBRE_JOURS_PAR_ANNEE = 365.25;
    public const NOMBRE_JOURS_PAR_MOIS = 30;

    public static \DateTime $aujourdhui;

    /**
     * @return bool
     */
    public static function intervallePlusGandQueAns(\DateTime $dateDebut, \DateTime $dateFin = null)
    {
        if (null === $dateFin) {
            return false;
        }

        $intervalle = $dateDebut->diff($dateFin);

        return $intervalle->format('%a') >= 366 ? true : false;
    }

    public static function intervaleMoinsUnMois(\DateTime $dateDebut, \DateTime $dateFin = null): bool
    {
        if (null === $dateFin) {
            return false;
        }

        $intervalle = $dateDebut->diff($dateFin);

        return $intervalle->format('%a') <= self::NOMBRE_JOURS_PAR_MOIS ? true : false;
    }

    /**
     * @return string
     */
    public static function getAns(\DateTime $date)
    {
        return $date->format('Y');
    }

    /**
     * @return string
     */
    public static function getMois(\DateTime $date)
    {
        setlocale(\LC_TIME, 'fr_FR', 'French');

        return ucfirst(utf8_encode(strftime('%b', strtotime($date->format('Y-m-d')))));
    }

    public static function getDateByChaineMysql(?string $dateMysql): ?\DateTime
    {
        if (empty($dateMysql)) {
            return null;
        }

        $date = \DateTime::createFromFormat('Y-m-d', $dateMysql);

        if (false === $date) {
            return null;
        }

        return $date->setTime(0, 0);
    }

    /**
     * @return string
     */
    public static function getPeriode(\DateTime $date, bool $parMois, ?bool $parJour = false)
    {
        if ($parJour) {
            return $date->format(self::FORMAT);
        }

        return $parMois ? self::getMois($date).' '.self::getAns($date) : self::getAns($date);
    }

    public static function getListeJoursEntreDeuxDate(\DateTime $dateDebut, ?\DateTime $dateFin): array
    {
        $liste = [];

        if (null === $dateFin) {
            $liste[] = self::getPeriode($dateDebut, false, true);

            return $liste;
        }

        $date = new \DateTime($dateDebut->format('d-m-Y'));

        while ($date <= $dateFin) {
            $liste[] = self::getPeriode($date, false, true);
            $date->modify('tomorrow');
        }

        return $liste;
    }

    public static function getListeMoisEntreDeuxDate(\DateTime $dateDebut, ?\DateTime $dateFin): array
    {
        $listeMois = [];

        if (null === $dateFin) {
            $listeMois[] = self::getPeriode($dateDebut, true);

            return $listeMois;
        }

        $date = new \DateTime($dateDebut->format('d-m-Y'));

        while ($date <= $dateFin) {
            $listeMois[] = self::getPeriode($date, true);
            $date->modify('first day of next month');
        }

        return $listeMois;
    }

    public static function getListeAnsEntreDeuxDate(\DateTime $dateDebut, ?\DateTime $dateFin): array
    {
        $listeAns = [];

        if (null === $dateFin) {
            $listeAns[] = self::getPeriode($dateDebut, false);

            return $listeAns;
        }

        $date = new \DateTime($dateDebut->format('d-m-Y'));

        while ($date <= $dateFin) {
            $listeAns[] = self::getPeriode($date, false);
            $date->modify('1st January Next Year');
        }

        return $listeAns;
    }

    public static function getIntervaleEntreDeuxDate(\DateTime $dateDebut, \DateTime $dateFin = null): int
    {
        $aujoudhui = self::getAujourdhui();
        $intervale = null === $dateFin ? $dateDebut->diff($aujoudhui) : $dateDebut->diff($dateFin);

        return (int) $intervale->format('%a') + 1;
    }

    public static function isDeuxDatesEgales(?\DateTime $date1, ?\DateTime $date2)
    {
        if (null === $date1) {
            return false;
        }

        if (null === $date2) {
            return false;
        }

        $intervale = $date1->diff($date2);

        return 0 === $intervale->days;
    }

    /**
     * Vérifie si la date est au format JJ/MM/AAAA.
     */
    public static function isDateFormatFrancais(string $date): bool
    {
        return 1 === preg_match('/^\\d{2}\/\\d{2}\/\\d{4}$/', $date);
    }

    public static function getListePeriodeEntreDeuxDates(\DateTime $dateDebut, \DateTime $dateFin): array
    {
        $estPlusUnAns = self::intervallePlusGandQueAns($dateDebut, $dateFin);

        $estMoinsUnMois = self::intervaleMoinsUnMois($dateDebut, $dateFin);

        if ($estMoinsUnMois) {
            $liste = self::getListeJoursEntreDeuxDate($dateDebut, $dateFin);
        } elseif ($estPlusUnAns) {
            $liste = self::getListeAnsEntreDeuxDate($dateDebut, $dateFin);
        } else {
            $liste = self::getListeMoisEntreDeuxDate($dateDebut, $dateFin);
        }

        return $liste;
    }

    public static function getAujourdhui(): \DateTime
    {
        if (!isset(self::$aujourdhui)) {
            self::$aujourdhui = (new \DateTime())->setTime(0, 0);
        }

        return self::$aujourdhui;
    }

    public static function getDateHier(): \DateTime
    {
        return (new \DateTime('- 1 day'))->setTime(0, 0);
    }
}
