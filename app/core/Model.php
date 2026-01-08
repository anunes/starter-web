<?php

namespace app\core;

use app\core\Database;

class Model
{
    protected Database $db;
    public function __construct()
    {
        $this->db = new Database();
    }

    public function all($table)
    {
        return $this->db->rows("SELECT * FROM $table");
    }

    public function activeUsers()
    {
        if ($this->hasColumn('users', 'active')) {
            return $this->db->rows("SELECT * FROM users WHERE active = 1");
        }
        return $this->db->rows("SELECT * FROM users");
    }

    public function inactiveUsers()
    {
        if ($this->hasColumn('users', 'active')) {
            return $this->db->rows("SELECT * FROM users WHERE active = 0");
        }
        return [];
    }

    public function find($table, $id)
    {
        return $this->db->row("SELECT * FROM $table WHERE id = ?", [$id]);
    }

    public function update($table, $data, $id)
    {
        return $this->db->update($table, $data, $where = ['id' => $id]);
    }

    public function softDelete($table, $id)
    {
        if ($this->hasColumn($table, 'active')) {
            $active = ['active' => 0];
            return $this->db->update($table, $active, $where = ['id' => $id]);
        }
        return 0;
    }

    public function recoverDeletedUser($table, $id)
    {
        if ($this->hasColumn($table, 'active')) {
            $active = ['active' => 1];
            return $this->db->update($table, $active, $where = ['id' => $id]);
        }
        return 0;
    }

    public function hasColumn(string $table, string $column): bool
    {
        $pdo = $this->db->getPdo();
        $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        try {
            if ($driver === 'sqlite') {
                $stmt = $this->db->run("PRAGMA table_info($table)");
                $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($columns as $col) {
                    if (($col['name'] ?? '') === $column) {
                        return true;
                    }
                }
                return false;
            }

            if ($driver === 'pgsql') {
                $stmt = $this->db->run(
                    "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = current_schema() AND table_name = ? AND column_name = ?",
                    [$table, $column]
                );
                return (int) $stmt->fetchColumn() > 0;
            }

            $stmt = $this->db->run(
                "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?",
                [DB_NAME, $table, $column]
            );
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable $error) {
            return false;
        }
    }

    public function delete($table, $id)
    {
        return $this->db->delete("DELETE FROM $table WHERE id = ?", [$id]);
    }

    public function settings(): ?object
    {
        return $this->db->row("SELECT * FROM settings LIMIT 1");
    }

    public function updateSettings(array $data): void
    {
        $current = $this->settings();
        if (!$current) {
            $this->db->insert('settings', $data);
            return;
        }

        if (isset($current->id)) {
            $this->db->update('settings', $data, ['id' => $current->id]);
            return;
        }

        $columns = [];
        $values = [];
        foreach ($data as $key => $value) {
            $columns[] = "$key = ?";
            $values[] = $value;
        }

        $sql = "UPDATE settings SET " . implode(', ', $columns);
        $this->db->run($sql, $values);
    }
}
