<?php

namespace App\Repositories;

use App\Models\KmlFile;
use App\Repositories\BaseRepository;

class KmlFileRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'file_path',
        'user_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }
    public function whereUserId($userId)
    {
        return KmlFile::where('user_id', $userId)->first();
    }
    public function model(): string
    {
        return KmlFile::class;
    }
}
