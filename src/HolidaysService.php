<?php

namespace EscoHolidays;

use DateTime;
use DateInterval;

class HolidaysService
{
    /**
     * Święta stałe (ustawowo wolne od pracy)
     *
     * @var array
     */
    private static $holidays = array(
        '0101' => 'Nowy Rok',
        '0106' => 'Święto Trzech Króli',
        '0501' => '1 Maja',
        '0503' => '3 Maja',
        '0815' => 'WNP',
        '1101' => 'Wszystkich Świętych',
        '1111' => 'Dzień Niepodległości',
        '1225' => 'Boże Narodzenie',
        '1226' => 'Boże Narodzenie (2gi dzień)',
    );

    /**
     * Zwraca wszystkie święta pomiędzy dwoma datami
     *
     * @param DateTime $fromDate
     * @param DateTime $toDate
     * @return array
     * @throws Exception\LogicException
     */
    public static function getHolidaysBetween(DateTime $fromDate, DateTime $toDate)
    {
        //święta ruchome
        $movingHolidays = array();
        for ($year=(int)$fromDate->format('Y'); $year<=(int)$toDate->format('Y'); $year++) {
            $easterDay = self::getEasterDay($year);
            $easterMonday = new DateTime($easterDay->format('Y-m-d') . ' + 1 day');
            $greenDays = new DateTime($easterDay->format('Y-m-d') . ' + 49 days');
            $corpusChristi = new DateTime($easterDay->format('Y-m-d') . ' + 60 days');
            $movingHolidays[$easterDay->format('Ymd')] = 'Wielkanoc';
            $movingHolidays[$easterMonday->format('Ymd')] = 'Poniedziałek Wielkanocny';
            $movingHolidays[$greenDays->format('Ymd')] = 'Zielone Świątki';
            $movingHolidays[$corpusChristi->format('Ymd')] = 'Boże Ciało';
        }

        $result = array();
        $date = clone $fromDate;
        while ($date <= $toDate) {
            if (array_key_exists($date->format('md'), self::$holidays)) {
                $result[$date->format('Ymd')] = self::$holidays[$date->format('md')];
            }
            if (array_key_exists($date->format('Ymd'), $movingHolidays)) {
                $result[$date->format('Ymd')] = $movingHolidays[$date->format('Ymd')];
            }
            $date->add(new DateInterval('P1D'));
        }
        ksort($result);

        return $result;
    }

    /**
     * Oblicza dzień obchodzenia Wielkanocy w podanym roku wg algorytmu Gaussa
     *
     * @param int $year Rok z zakresu lat 1582 do 2499
     * @return DateTime
     * @throws Exception\LogicException
     */
    private static function getEasterDay($year)
    {
        if ($year >= 2500) {
            throw new Exception\LogicException('Nieobsługiwany zakres dat do obliczenia Wielkanocy');
        }

        $a = $year % 19;
        $b = $year % 4;
        $c = $year % 7;
        $factors = self::getEasterFactors($year);
        $d = ($a*19 + $factors['factorA']) % 30;
        $e = (2*$b + 4*$c + 6*$d + $factors['factorB']) % 7;

        if (($d === 29 || $d === 28) && $e === 6) {
            $d -= 7;
        }
        $easterDay = new DateTime('22-03-' . $year . ' + ' . ($d + $e) . ' days');

        return $easterDay;
    }

    /**
     * Zwraca współczynnik do obliczenia daty Wielkanocy na podstawie podanego roku
     *
     * @param int $year Rok z zakresu lat 1582 do 2499
     * @return array
     * @throws \Exception
     */
    private static function getEasterFactors($year)
    {
        if ($year <= 1582) {
            $factorA = 15;
            $factorB = 6;
        } elseif ($year >= 1583 && $year <= 1699) {
            $factorA = 22;
            $factorB = 2;
        } elseif ($year >= 1700 && $year <= 1799) {
            $factorA = 23;
            $factorB = 3;
        } elseif ($year >= 1800 && $year <= 1899) {
            $factorA = 23;
            $factorB = 4;
        } elseif ($year >= 1900 && $year <= 2099) {
            $factorA = 24;
            $factorB = 5;
        } elseif ($year >= 2100 && $year <= 2199) {
            $factorA = 24;
            $factorB = 6;
        } elseif ($year >= 2200 && $year <= 2299) {
            $factorA = 25;
            $factorB = 0;
        } elseif ($year >= 2300 && $year <= 2399) {
            $factorA = 26;
            $factorB = 1;
        } elseif ($year >= 2400 && $year <= 2499) {
            $factorA = 25;
            $factorB = 1;
        } else {
            throw new Exception\LogicException('Nieobsługiwany zakres dat do obliczenia Wielkanocy');
        }

        return array(
            'factorA' => $factorA,
            'factorB' => $factorB,
        );
    }
}