<?php

namespace App\Enum\Source;

enum SourceTypes: string
{
    case Database = 'database';
    case Rss = 'rss';
    case Api = 'api';
    case File = 'file';
}