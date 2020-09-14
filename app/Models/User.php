<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;
use Config\Database;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['first_name', 'last_name', 'email', 'passwd'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    public function emailExists(string $email): bool
    {
        $db = Database::connect();
        $query = $db->query('SELECT * FROM users WHERE email = ?', [$email]);
        $result = $query->getResult();

        if(count($result) > 0){
            return true;
        }

        return false;
    }

    public function findByEmail(string $email): array
    {
        $db = Database::connect();
        $query = $db->query('SELECT * FROM users WHERE email = ?', [$email]);
        $result = $query->getResultArray();

        if(count($result) > 0){
            return $result;
        }

        return [];
    }

    public function findById(string $id)
    {
        $db = Database::connect();
        $query = $db->query('SELECT * FROM users WHERE id = ?', [$id]);
        $result = $query->getResult();

        if(count($result) > 0){
            return $result;
        }

        return [];
    }
}