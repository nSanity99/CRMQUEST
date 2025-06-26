<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carica autoload di Composer
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    error_log('[order_mailer] vendor/autoload.php mancante - eseguire `composer install`.');
    return;                                 // esce silenziosamente se manca la libreria
}

// Carica configurazione SMTP (solo variabili!)
require_once __DIR__ . '/mailer_config.php';

/**
 * Invia l’e-mail di notifica ordine e lascia un log dettagliato nel file PHP.
 *
 * @param int    $orderId         ID univoco dell’ordine appena inserito
 * @param string $nomeRichiedente Nome dell’utente che ha inviato l’ordine
 * @param string $centroCosto     Centro di costo selezionato
 * @param array  $prodotti        Array di prodotti (name, quantity, unit, notes)
 */
function sendOrderNotification($orderId, $nomeRichiedente, $centroCosto, array $prodotti)
{
    error_log("[order_mailer] ► INIZIO sendOrderNotification per ordine #{$orderId}");

    $mail = new PHPMailer(true);

    // Debug (puoi disattivarlo in produzione)
    $mail->SMTPDebug   = 0; // Impostato a 0 per evitare log verbosi in produzione, usa 2 per debug
    $mail->Debugoutput = function ($str, $level) {
        error_log("[PHPMailer][$level] $str");
    };

    try {
        // Configurazione SMTP
        $mail->isSMTP();
        $mail->Host       = $GLOBALS['mail_host'];
        $mail->Port       = $GLOBALS['mail_port'];
        $mail->SMTPSecure = $GLOBALS['mail_secure'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $GLOBALS['mail_username'];
        $mail->Password   = $GLOBALS['mail_password'];
        $mail->CharSet    = 'UTF-8'; // Imposta la codifica corretta

        // Mittente e destinatario
        $mail->setFrom($GLOBALS['mail_from'], $GLOBALS['mail_from_name']);
        $mail->addAddress($GLOBALS['mail_to']);
        
        // --- NOVITÀ: Incorpora il logo nella mail ---
        // Assicurati che il percorso al logo sia corretto rispetto a questo file PHP
        $logoPath = __DIR__ . '/../../assets/logo.png';
        if (file_exists($logoPath)) {
            $mail->addEmbeddedImage($logoPath, 'logo_cid');
        } else {
            error_log("[order_mailer] Logo non trovato al percorso: " . $logoPath);
        }

        // Contenuto della Mail
        $mail->isHTML(true);
        $mail->Subject = "Nuovo Ordine #{$orderId} - CRM Gruppo Vitolo";

        // Link diretto all'ordine
        $linkOrdine = "https://crmgv.contact.local/gestioneordini.php";

        // --- INIZIO NUOVO TEMPLATE HTML PROFESSIONALE ---
        
        $productListHtml = '';
        foreach ($prodotti as $p) {
            $nome = htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8');
            $qty  = (int)($p['quantity'] ?? 0);
            $unit = htmlspecialchars($p['unit'] ?? '', ENT_QUOTES, 'UTF-8');
            $note = htmlspecialchars($p['notes'] ?? '', ENT_QUOTES, 'UTF-8');

            $noteHtml = $note ? '<br><span style="font-size: 14px; color: #666; font-style: italic;">Note: ' . $note . '</span>' : '';

            $productListHtml .= '<li style="padding-bottom: 15px; border-bottom: 1px solid #eeeeee; margin-bottom: 15px;">' .
                                  '<strong style="color: #333333;">' . $nome . '</strong> &ndash; ' . $qty . ' ' . $unit .
                                  $noteHtml .
                                '</li>';
        }
        // Rimuove l'ultimo bordo
        $productListHtml = preg_replace('/border-bottom: 1px solid #eeeeee;/', '', $productListHtml, 1);


        $body = <<<HTML
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Ordine Ricevuto</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: #ffffff; border: 1px solid #dddddd; border-radius: 8px; overflow: hidden;">
                    
                    <tr>
                        <td align="center" style="padding: 30px 20px 20px 20px; background-color: #ffffff;">
                            <img src="cid:logo_cid" alt="Gruppo Vitolo Logo" width="180" style="display: block;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 30px 10px 30px;">
                            <h2 style="margin: 0; color: #2c3e50; font-size: 24px; font-weight: bold;">Nuovo Ordine Ricevuto</h2>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 10px 30px;">
                            <p style="margin: 0; font-size: 16px; color: #555555; line-height: 1.5;">
                                È stato registrato un nuovo ordine nel sistema CRM.
                            </p>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 20px; background-color: #f8f9fa; border: 1px solid #eeeeee; border-radius: 6px; padding: 15px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; font-size: 16px; color: #333333;"><strong>Numero Ordine:</strong> #{$orderId}</p>
                                        <p style="margin: 10px 0 0 0; font-size: 16px; color: #333333;"><strong>Richiedente:</strong> {$nomeRichiedente}</p>
                                        <p style="margin: 10px 0 0 0; font-size: 16px; color: #333333;"><strong>Centro di Costo:</strong> {$centroCosto}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 20px 30px;">
                            <h3 style="margin: 0 0 15px 0; color: #2c3e50; font-size: 20px; border-bottom: 2px solid #c5a052; padding-bottom: 5px;">Riepilogo Prodotti</h3>
                            <ul style="margin: 0; padding: 0; list-style-type: none; font-size: 16px; color: #555555;">
                                {$productListHtml}
                            </ul>
                        </td>
                    </tr>
                    
                    <tr>
                        <td align="center" style="padding: 20px 30px 40px 30px;">
                            <a href="{$linkOrdine}" style="display: inline-block; padding: 14px 28px; background-color: #c5a052; color: #ffffff !important; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 16px;">
                                Visualizza e Gestisci l'Ordine
                            </a>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f2f2f2;">
                            <p style="margin: 0; font-size: 12px; color: #999999; text-align: center; line-height: 1.5;">
                                Questa è una notifica automatica inviata dal sistema CRM Gruppo Vitolo.<br>
                                Si prega di non rispondere a questa email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
        // --- FINE NUOVO TEMPLATE HTML PROFESSIONALE ---

        $mail->Body = $body;
        $mail->send();
        error_log("[order_mailer] ✔ Email ordine #{$orderId} inviata con successo.");
    } catch (Exception $e) {
        error_log('[order_mailer] ✖ Exception: ' . $e->getMessage());
        error_log('[order_mailer] ✖ ErrorInfo: ' . $mail->ErrorInfo);
    }

    error_log("[order_mailer] ◄ FINE sendOrderNotification per ordine #{$orderId}");
}