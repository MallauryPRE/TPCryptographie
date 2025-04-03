# Chiffrement Symétrique (AES-256) & Asymétrique (RSA)

Ce projet est une application web en PHP permettant de chiffrer et déchiffrer des messages en utilisant :

- **AES-256** pour le chiffrement symétrique
- **RSA** (2048 bits) pour le chiffrement asymétrique

## 🚀 Prérequis

- PHP 7.4+
- OpenSSL activé
- Serveur local (Apache, Nginx ou PHP Built-in Server)

## 📂 Installation

1. Clonez le projet :
   ```bash
   git clone https://github.com/MallauryPRE/TPCryptographie.git
   cd TPCryptographie
   ```
2. Créez un fichier `.env` à la racine :
   ```ini
   SECRET_KEY="votre_cle_secrete_32_bytes"
   SECRET_IV="votre_vecteur_initialisation_16_bytes"
   ```
4. Lancez un serveur PHP local :
   ```bash
   php -S localhost:8000
   ```
5. Ouvrez votre navigateur et accédez à `http://localhost:8000`

## 🔐 Fonctionnalités

### Chiffrement Symétrique (AES-256)

- Chiffrement avec une clé secrète stockée dans `.env`
- Déchiffrement avec la même clé

### Chiffrement Asymétrique (RSA)

- Génération automatique des clés RSA (`private_key.pem` et `public_key.pem`)
- Chiffrement avec la clé publique
- Déchiffrement avec la clé privée

## ⚠️ Sécurité

- **Utilisez des clés fortes** en AES (32 bytes pour `SECRET_KEY` et 16 bytes pour `SECRET_IV`)

## 📜 Licence

Ce projet est libre d'utilisation sous licence MIT.
