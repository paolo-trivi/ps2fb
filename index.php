<?php
// PrestaShop Meta Catalog Export
// Version: 1.0
$dbHost = "host";
$dbUsername = "user";
$dbPassword = "password";
$dbName = "database";
$psUrl = "https://www.tuosito.it/"; // URL PrestaShop

// Database connection
// Assicurati di avere l'estensione mysqli abilitata
if (!extension_loaded('mysqli')) {
    die("L'estensione mysqli non è abilitata.");
}
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Esportazione per Catalogo Meta
if (isset($_GET['export']) && $_GET['export'] === 'csv') {

    $sql = "
        SELECT
            a.id_product AS id,
            b.name AS title,
            TRIM(REPLACE(REPLACE(ExtractValue(b.description, '//text()'), '\n', ' '), '\r', '')) AS description,
            ROUND(a.price + (a.price * t.rate / 100), 2) AS price,
            ma.name AS brand,
            IF(sav.quantity > 0, 'in stock', 'out of stock') AS availability,
            'new' AS `condition`,
            CONCAT('$psUrl', a.id_product, '-', b.link_rewrite, '.html') AS link,
            CONCAT('$psUrl', i.id_image, '-thickbox_default/image.jpg') AS image_link,
            a.reference AS mpn,
            a.ean13 AS gtin,
            cl.name AS product_type,
            a.id_category_default AS item_group_id
        FROM ps_product a
        LEFT JOIN ps_product_lang b ON (
            b.id_product = a.id_product AND b.id_lang = 1 AND b.id_shop = 1
        )
        LEFT JOIN ps_tax_rule tr ON (
            tr.id_tax_rules_group = a.id_tax_rules_group AND tr.id_country = 10
        )
        LEFT JOIN ps_tax t ON t.id_tax = tr.id_tax
        LEFT JOIN ps_manufacturer ma ON ma.id_manufacturer = a.id_manufacturer
        LEFT JOIN ps_stock_available sav ON (
            sav.id_product = a.id_product AND sav.id_product_attribute = 0 AND sav.id_shop = 1 AND sav.id_shop_group = 0
        )
        JOIN ps_product_shop sa ON (
            a.id_product = sa.id_product AND sa.id_shop = a.id_shop_default
        )
        LEFT JOIN ps_category_lang cl ON (
            sa.id_category_default = cl.id_category AND cl.id_lang = 1 AND cl.id_shop = a.id_shop_default
        )
        LEFT JOIN ps_image_shop image_shop ON (
            image_shop.id_product = a.id_product AND image_shop.cover = 1 AND image_shop.id_shop = a.id_shop_default
        )
        LEFT JOIN ps_image i ON i.id_image = image_shop.id_image
        WHERE a.active = 1
        ORDER BY a.id_product ASC
    ";

    $query = $db->query($sql);

    if ($query && $query->num_rows > 0) {
        $delimiter = ",";
        $filename = "meta_feed.csv";
        $f = fopen('php://memory', 'w');

        // Intestazioni Meta Catalog
        $fields = [
            'id',
            'title',
            'description',
            'availability',
            'condition',
            'price',
            'link',
            'image_link',
            'brand',
            'mpn',
            'gtin',
            'product_type',
            'item_group_id'
        ];
        fputcsv($f, $fields, $delimiter);

        while ($row = $query->fetch_assoc()) {
            $lineData = [
                $row['id'],
                $row['title'],
                html_entity_decode(strip_tags($row['description'] ?? '')),
                $row['availability'],
                $row['condition'],
                number_format($row['price'], 2, '.', '') . ' EUR',
                $row['link'],
                $row['image_link'],
                $row['brand'],
                $row['mpn'],
                $row['gtin'],
                $row['product_type'],
                $row['item_group_id']
            ];
            fputcsv($f, $lineData, $delimiter);
        }

        fseek($f, 0);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        fpassthru($f);
        exit();
    } else {
        die("Nessun prodotto disponibile.");
    }
}

// Redirect to PrestaShop URL
header("Location: " . $psUrl);
exit();