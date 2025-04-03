<?php

$env = parse_ini_file(__DIR__ . '/.env');

if (!$env) {
    die("Erreur : impossible de charger le fichier .env");
}

$secretKey = $env["SECRET_KEY"] ?? die("Erreur : SECRET_KEY non défini !");
$secretIv = $env["SECRET_IV"] ?? die("Erreur : SECRET_IV non défini !");

// ===== Chiffrement symétrique (AES-256) =====
function encryptData($data, $key, $iv) {
    return base64_encode(openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv));
}

function decryptData($encryptedData, $key, $iv) {
    return openssl_decrypt(base64_decode($encryptedData), 'aes-256-cbc', $key, 0, $iv);
}

$encryptedText = "";
$decryptedText = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["encrypt"])) {
        $encryptedText = encryptData($_POST["text_to_encrypt"] ?? '', $secretKey, $secretIv);
    } elseif (isset($_POST["decrypt"])) {
        $decryptedText = decryptData($_POST["text_to_decrypt"] ?? '', $secretKey, $secretIv);
    }
}

// ===== Chiffrement Asymétrique (RSA) =====

$privateKeyFile = __DIR__ . '/private_key.pem';
$publicKeyFile = __DIR__ . '/public_key.pem';

if (!file_exists($privateKeyFile) || !file_exists($publicKeyFile)) {
    $config = [
        "private_key_bits" => 2048,
        "default_md" => "sha256",
    ];
    $res = openssl_pkey_new($config);

    openssl_pkey_export($res, $privateKey);
    $publicKeyDetails = openssl_pkey_get_details($res);
    $publicKey = $publicKeyDetails["key"];

    file_put_contents($privateKeyFile, $privateKey);
    file_put_contents($publicKeyFile, $publicKey);
}


$privateKey = file_get_contents($privateKeyFile);
$publicKey = file_get_contents($publicKeyFile);


function encryptWithPublicKey($data, $publicKey) {
    openssl_public_encrypt($data, $encrypted, $publicKey);
    return base64_encode($encrypted);
}

function decryptWithPrivateKey($encryptedData, $privateKey) {
    openssl_private_decrypt(base64_decode($encryptedData), $decrypted, $privateKey);
    return $decrypted;
}

$asymEncryptedText = "";
$asymDecryptedText = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["asym_encrypt"])) {
        $asymEncryptedText = encryptWithPublicKey($_POST["asym_text_to_encrypt"] ?? '', $publicKey);
    } elseif (isset($_POST["asym_decrypt"])) {
        $asymDecryptedText = decryptWithPrivateKey($_POST["asym_text_to_decrypt"] ?? '', $privateKey);
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chiffrement AES-256 & RSA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
            background-color: #f4f4f4;
            color: #333;
        }
        h2 {
            color: #2c3e50;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        input, button, textarea {
            padding: 12px;
            margin: 10px 0;
            width: 90%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .output {
            font-weight: bold;
            color: green;
            word-wrap: break-word;
        }
        hr {
            border: 1px solid #ddd;
            margin: 40px 0;
        }
    </style>
</head>
<body>
    <h2>🔐 Chiffrement & Déchiffrement AES-256</h2>

    <form method="post">
        <h3>🔑 Chiffrement Symétrique</h3>
        <input type="text" name="text_to_encrypt" placeholder="Texte à chiffrer" required>
        <button type="submit" name="encrypt">Chiffrer</button>
    </form>
    <?php if ($encryptedText): ?>
        <p class="output">📝 Texte chiffré : <br><textarea readonly><?= htmlspecialchars($encryptedText) ?></textarea></p>
    <?php endif; ?>

    <form method="post">
        <h3>🔓 Déchiffrement Symétrique</h3>
        <input type="text" name="text_to_decrypt" placeholder="Texte chiffré à déchiffrer" required>
        <button type="submit" name="decrypt">Déchiffrer</button>
    </form>
    <?php if ($decryptedText): ?>
        <p class="output">✅ Texte déchiffré : <br><textarea readonly><?= htmlspecialchars($decryptedText) ?></textarea></p>
    <?php endif; ?>

    <hr>

    <h2>🔑 Chiffrement & Déchiffrement RSA</h2>

    <form method="post">
        <h3>🔒 Chiffrement Asymétrique</h3>
        <input type="text" name="asym_text_to_encrypt" placeholder="Texte à chiffrer" required>
        <button type="submit" name="asym_encrypt">Chiffrer</button>
    </form>
    <?php if ($asymEncryptedText): ?>
        <p class="output">📝 Texte chiffré : <br><textarea readonly><?= htmlspecialchars($asymEncryptedText) ?></textarea></p>
    <?php endif; ?>

    <form method="post">
        <h3>🔓 Déchiffrement Asymétrique</h3>
        <input type="text" name="asym_text_to_decrypt" placeholder="Texte chiffré à déchiffrer" required>
        <button type="submit" name="asym_decrypt">Déchiffrer</button>
    </form>
    <?php if ($asymDecryptedText): ?>
        <p class="output">✅ Texte déchiffré : <br><textarea readonly><?= htmlspecialchars($asymDecryptedText) ?></textarea></p>
    <?php endif; ?>
</body>
</html>
