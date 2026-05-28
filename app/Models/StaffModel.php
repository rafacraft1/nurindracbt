<?php

namespace App\Models;

use CodeIgniter\Model;

class StaffModel extends Model
{
    protected $table            = 'staff';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['username', 'password', 'nama_lengkap', 'role', 'is_panitia', 'created_at'];

    public function countByRole(string $role): int
    {
        return $this->where('role', $role)->countAllResults();
    }

    public function countPanitia(): int
    {
        return $this->where('is_panitia', 1)->countAllResults();
    }
}
