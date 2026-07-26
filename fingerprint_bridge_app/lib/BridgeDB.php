<?php
/**
 * Database Engine untuk Fingerprint Bridge App (SQLite Engine)
 * Menyimpan log presensi hasil tarik data dari mesin ke dalam database lokal
 * dan menghapusnya secara otomatis setelah berhasil dikirim ke Web API Server.
 */
class BridgeDB
{
    private static $pdo = null;

    public static function getPDO()
    {
        if (self::$pdo === null) {
            $db_dir = __DIR__ . '/../data';
            if (!is_dir($db_dir)) {
                @mkdir($db_dir, 0777, true);
            }
            $db_file = $db_dir . '/bridge_logs.db';

            self::$pdo = new PDO("sqlite:" . $db_file);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Inisialisasi Tabel bridge_scanlogs jika belum ada
            self::$pdo->exec("
                CREATE TABLE IF NOT EXISTS bridge_scanlogs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    sn TEXT,
                    pin TEXT NOT NULL,
                    scan_date TEXT NOT NULL,
                    verifymode INTEGER DEFAULT 1,
                    iomode INTEGER DEFAULT 0,
                    workcode INTEGER DEFAULT 0,
                    status TEXT DEFAULT 'pending',
                    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(pin, scan_date)
                );
            ");
        }
        return self::$pdo;
    }

    /**
     * Simpan array log hasil ditarik dari mesin ke database SQLite
     */
    public static function insertScanlogs(array $logs)
    {
        if (empty($logs)) return 0;
        $pdo  = self::getPDO();
        $stmt = $pdo->prepare("
            INSERT OR IGNORE INTO bridge_scanlogs (sn, pin, scan_date, verifymode, iomode, workcode, status)
            VALUES (:sn, :pin, :scan_date, :verifymode, :iomode, :workcode, 'pending')
        ");

        $inserted = 0;
        $pdo->beginTransaction();
        foreach ($logs as $l) {
            if (!is_array($l)) continue;
            $pin  = trim((string)($l['pin'] ?? $l['PIN'] ?? $l['user_id'] ?? $l['UserId'] ?? ''));
            $date = trim((string)($l['scan_date'] ?? $l['ScanDate'] ?? $l['date'] ?? $l['Date'] ?? ''));

            if ($pin !== '' && $date !== '') {
                $stmt->execute([
                    ':sn'         => trim((string)($l['sn'] ?? 'FS-EASYLINK')),
                    ':pin'        => $pin,
                    ':scan_date'  => $date,
                    ':verifymode' => intval($l['verifymode'] ?? $l['VerifyMode'] ?? 1),
                    ':iomode'     => intval($l['iomode'] ?? $l['IOMode'] ?? 0),
                    ':workcode'   => intval($l['workcode'] ?? $l['WorkCode'] ?? 0)
                ]);
                if ($stmt->rowCount() > 0) {
                    $inserted++;
                }
            }
        }
        $pdo->commit();
        return $inserted;
    }

    /**
     * Ambil data log yang siap dikirim (status = 'pending')
     */
    public static function getPendingScanlogs($limit = 10000)
    {
        $pdo  = self::getPDO();
        $stmt = $pdo->prepare("SELECT id, pin, scan_date, sn FROM bridge_scanlogs WHERE status = 'pending' ORDER BY id ASC LIMIT :limit");
        $stmt->bindValue(':limit', intval($limit), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Hitung jumlah log berstatus pending
     */
    public static function countPendingScanlogs()
    {
        $pdo = self::getPDO();
        return (int)$pdo->query("SELECT COUNT(*) FROM bridge_scanlogs WHERE status = 'pending'")->fetchColumn();
    }

    /**
     * Hitung total seluruh log di database
     */
    public static function countTotalScanlogs()
    {
        $pdo = self::getPDO();
        return (int)$pdo->query("SELECT COUNT(*) FROM bridge_scanlogs")->fetchColumn();
    }

    /**
     * Hapus log yang sudah sukses dikirim ke server Web API
     */
    public static function deleteSentLogs(array $ids = [])
    {
        $pdo = self::getPDO();
        if (empty($ids)) {
            return $pdo->exec("DELETE FROM bridge_scanlogs WHERE status = 'pending' OR status = 'sent'");
        }
        $in   = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("DELETE FROM bridge_scanlogs WHERE id IN ($in)");
        $stmt->execute($ids);
        return $stmt->rowCount();
    }

    /**
     * Hapus seluruh log di database (Clear All)
     */
    public static function clearAllLogs()
    {
        $pdo = self::getPDO();
        return $pdo->exec("DELETE FROM bridge_scanlogs");
    }
}
