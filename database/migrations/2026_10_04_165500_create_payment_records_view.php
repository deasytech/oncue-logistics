<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  public function up(): void
  {
    DB::statement('DROP VIEW IF EXISTS payment_records');

    DB::statement(<<<'SQL'
            CREATE VIEW payment_records AS
            SELECT
                CONCAT('package-', pp.id) AS payment_key,
                pp.id AS source_id,
                'package' AS type,
                pp.reference AS reference,
                TRIM(CONCAT(COALESCE(c.title, ''), ' ', COALESCE(c.first_name, ''), ' ', COALESCE(c.last_name, ''))) AS payer_name,
                c.email AS payer_email,
                pp.amount AS amount,
                pp.status AS status,
                'online' AS payment_method,
                pp.created_at AS created_at,
                pp.updated_at AS updated_at
            FROM package_payments pp
            LEFT JOIN customers c ON c.id = pp.customer_id

            UNION ALL

            SELECT
                CONCAT('fabric-', gfs.id) AS payment_key,
                gfs.id AS source_id,
                'fabric' AS type,
                gfs.payment_reference AS reference,
                TRIM(CONCAT(COALESCE(g.title, ''), ' ', COALESCE(g.first_name, ''), ' ', COALESCE(g.last_name, ''))) AS payer_name,
                g.email AS payer_email,
                gfs.total_amount AS amount,
                CASE
                    WHEN gfs.payment_status = 'paid' THEN 'success'
                    ELSE gfs.payment_status
                END AS status,
                gfs.payment_method AS payment_method,
                gfs.created_at AS created_at,
                gfs.updated_at AS updated_at
            FROM guest_fabric_selections gfs
            LEFT JOIN guests g ON g.id = gfs.guest_id
        SQL);
  }

  public function down(): void
  {
    DB::statement('DROP VIEW IF EXISTS payment_records');
  }
};
