<connect_postgres class="php">
    <?php 
        $host = "localhost";
        $dbname = "retro";
        $user = "postgres";
        $pass = "postgres";

        try {
            $pdo = new PDO(
                "pgsql:host=$host;dbname=$dbname",
                $user,
                $pass
            );
        } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
        }
    ?>
</connect_postgres>