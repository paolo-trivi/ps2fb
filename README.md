# ps2fb
PHP script for export Prestashop product to Facebook catalog.

# Install
- Copy folder in the root of Prestashop
- Modify database and url var in config.php
- Modify table prefix in exportData.php, right now are used ps_
- Check SEO link on the line

        CONCAT(\"$psUrl\", a.`id_product`,\"-\",b.`link_rewrite`,\".html\") AS `link`,     
        CONCAT(\"$psUrl\", i.`id_image`,\"-thickbox_default/image.jpg\") AS `image_link`

# Export
Add feed on facebook true link https://domain/ps2fb/exportData.php
