<?php
namespace App\Domain\ValueObject;

enum Priority: string {
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}