# PrestaShop to Meta (Facebook) Product Feed

Genera un **feed CSV compatibile con Meta Business Suite** (Facebook Catalog) direttamente dal tuo shop PrestaShop.

---

## ✅ Requisiti

- PHP 7.0 o superiore
- PrestaShop 1.6 / 1.7 / 8.x
- Accesso FTP al server
- Modulo SEO Friendly URLs attivo

---

## 🚀 Installazione

1. **Scarica** o clona questa cartella nel root della tua installazione PrestaShop.  
   Esempio:

   ```
   /var/www/html/prestashop/ps2fb/
   ```

2. **Modifica la configurazione** nel file `index.php`, in particolare:

   - Dati del database:

     ```php
     $dbHost = "localhost";
     $dbUsername = "user";
     $dbPassword = "password";
     $dbName = "database_name";
     ```

   - URL del tuo negozio:

     ```php
     $psUrl = "https://www.prestashopwebsite.com/";
     ```

   - Prefisso delle tabelle PrestaShop (`ps_` di default):  
     Se hai un altro prefisso (es. `shop_`), aggiornalo in tutte le query SQL contenute in `index.php`.

3. **Controlla la generazione dei link SEO-friendly**, assicurati che il tuo PrestaShop generi URL del tipo:

   ```sql
   CONCAT('$psUrl', a.id_product, '-', b.link_rewrite, '.html') AS link
   CONCAT('$psUrl', i.id_image, '-thickbox_default/image.jpg') AS image_link
   ```

   Se utilizzi un modulo personalizzato per gli URL o una struttura diversa, modifica la query di conseguenza.

---

## 📤 Esportazione del Feed

Una volta configurato correttamente, puoi generare il CSV visitando l’URL:

```
https://www.prestashopwebsite.com/ps2fb/index.php?export=csv
```

Il file verrà scaricato automaticamente dal browser o potrà essere linkato direttamente a Meta Business Suite.

---

## ➕ Aggiunta del Feed su Meta Business Suite

1. Vai su **Meta Business Suite** > **Cataloghi** > **Aggiungi prodotti** > **Utilizza un feed**.
2. Inserisci l’URL pubblico del feed:
   ```
   https://www.prestashopwebsite.com/ps2fb/index.php?export=csv
   ```
3. Imposta una frequenza di aggiornamento (consigliato: ogni giorno).
4. Salva.

---

## 📌 Note aggiuntive

- I prodotti senza descrizione o immagine **non verranno rifiutati**, ma è fortemente consigliato che ogni prodotto ne abbia una.
- Il feed esporta solo prodotti **attivi e visibili**, con `active = 1`.
- Il campo `availability` viene generato da `ps_stock_available.quantity`.

---

## 🛠 Personalizzazioni possibili

- Aggiunta di GTIN/EAN, MPN o Google Product Category
- Inclusione di categorie, taglie o varianti
- Output XML anziché CSV
- Supporto multilingua

Contattaci per personalizzazioni!

---

## 🔒 Sicurezza

Il file `index.php` è accessibile pubblicamente. Se desideri proteggere il feed:

- Aggiungi un token `?export=csv&token=XYZ` e controllalo via PHP
- Proteggi con `.htaccess` o firewall

---

## 📄 Licenza

Distribuito con licenza **MIT**. Usalo liberamente, anche in ambito commerciale.
