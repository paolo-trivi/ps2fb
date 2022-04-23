<?php

// Load the configuration file
include_once 'config.php';

// Fetch records from database
$query = $db->query("SELECT
    a.`id_product` AS `id`,
    b.`name` AS `title`,
    /* b.`meta_description` AS `description`, */
    ExtractValue(b.`description`, '//text()') AS `description`,
    round(a.`price`+ (a.`price`*t.`rate`)/100, 2)  AS `price`,
    ma.`name`  AS `brand`,
    CONCAT(\"in stock\") AS `availability`,
    CONCAT(\"new\") AS `condition`,
    CONCAT(\"$psUrl\", a.`id_product`,\"-\",b.`link_rewrite`,\".html\") AS `link`,
    CONCAT(\"$psUrl\", i.`id_image`,\"-thickbox_default/image.jpg\") AS `image_link`


FROM
    `ps_product` a
LEFT JOIN
    `ps_product_lang` b
        ON (
            b.`id_product` = a.`id_product`
            AND b.`id_lang` = 1
            AND b.`id_shop` = 1
        )
LEFT JOIN
    `ps_tax_rule` tr
        ON (
            tr.`id_tax_rules_group` = a.`id_tax_rules_group`
            AND tr.`id_country` = 10
        )
LEFT JOIN
    `ps_tax` t
        ON (
            t.`id_tax` = tr.`id_tax`
        )
LEFT JOIN
    `ps_manufacturer` ma
        ON (
            ma.`id_manufacturer` = a.`id_manufacturer`
        )
LEFT JOIN
    `ps_stock_available` sav
        ON (
            sav.`id_product` = a.`id_product`
            AND sav.`id_product_attribute` = 0
            AND sav.id_shop = 1
            AND sav.id_shop_group = 0
        )
JOIN
    `ps_product_shop` sa
        ON (
            a.`id_product` = sa.`id_product`
            AND sa.id_shop = a.id_shop_default
        )
LEFT JOIN
    `ps_category_lang` cl
        ON (
            sa.`id_category_default` = cl.`id_category`
            AND b.`id_lang` = cl.`id_lang`
            AND cl.id_shop = a.id_shop_default
        )
LEFT JOIN
    `ps_shop` shop
        ON (
            shop.id_shop = a.id_shop_default
        )
LEFT JOIN
    `ps_image_shop` image_shop
        ON (
            image_shop.`id_product` = a.`id_product`
            AND image_shop.`cover` = 1
            AND image_shop.id_shop = a.id_shop_default
        )
LEFT JOIN
    `ps_image` i
        ON (
            i.`id_image` = image_shop.`id_image`
        )
LEFT JOIN
    `ps_product_download` pd
        ON (
            pd.`id_product` = a.`id_product`
            AND pd.`active` = 1
        )
WHERE
    1
ORDER BY
    a.`id_product` ASC");

if($query->num_rows > 0){
    $delimiter = ",";
    $filename = "prod-data.csv";

    // Create a file pointer
    $f = fopen('php://memory', 'w');

    // Set column headers
    $fields = array('id', 'title', 'description', 'price', 'brand', 'availability', 'condition', 'link', 'image_link');
    fputcsv($f, $fields, $delimiter);

    // Output each row of the data, format line as csv and write to file pointer
    while($row = $query->fetch_assoc()){
        $lineData = array($row['id'], $row['title'], $row['description'], $row['price'], $row['brand'], $row['availability'], $row['condition'], $row['link'], $row['image_link']);
        fputcsv($f, $lineData, $delimiter);
    }

    // Move back to beginning of file
    fseek($f, 0);

    // Set headers to download file rather than displayed
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

    //output all remaining data on a file pointer
    fpassthru($f);
}
exit;

?>
