<?php

// app/Enums/TypeEntreprise.php
namespace App\Enums;

enum TypeEntreprise: string
{
    case EI = 'EI';
    case SARL = 'SARL';
    case SA = 'SA';
    case SNC = 'SNC';

    public function label(): string
    {
        return match($this) {
            self::EI => 'Entreprise Individuelle',
            self::SARL => 'Société à Responsabilité Limitée',
            self::SA => 'Société Anonyme',
            self::SNC => 'Société en Nom Collectif',
        };
    }
}
