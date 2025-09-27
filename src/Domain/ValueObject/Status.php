<?php
namespace App\Domain\ValueObject;

enum Status: string {
        case Pending = 'pending';
        case InProgress = 'in_progress';
        case Completed = 'completed';
    }   