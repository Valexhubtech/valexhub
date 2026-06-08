<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Inbox extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'phosphor-envelope-duotone';
    
    protected static ?string $navigationLabel = 'Inbox';
    
    protected string $view = 'filament.pages.inbox';
    
    protected static ?int $navigationSort = 1;
}