# 1. Criar um novo projeto Laravel chamado "loja"
composer create-project laravel/laravel loja

# 2. Entrar na pasta do projeto
cd loja

# 3. Instalar o Breeze como dependência de desenvolvimento
composer require laravel/breeze --dev

# 4. Instalar o Breeze com Blade como stack e Pest
php artisan breeze:install blade --pest

# 5. Instalar dependências do frontend e compilar assets
npm install && npm run dev

# 6. Configurar o arquivo .env
# (você pode editar manualmente com um editor de texto ou com o comando abaixo:)
code .env  # se estiver usando VS Code (ou use outro editor)

# Substitua/adicione no .env:
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lojinha
DB_USERNAME=root
DB_PASSWORD=  # deixe em branco se não tiver senha no XAMPP

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com 
MAIL_PORT=587
MAIL_USERNAME=seuemail@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx  # App Password do Gmail (não é sua senha normal!)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seuemail@gmail.com
MAIL_FROM_NAME="Lojinha"

# 7. Ativar XAMPP: iniciar Apache e MySQL
***(isso é feito pela interface do XAMPP)***

# 8. Criar o banco de dados "lojinha"
php artisan migrate

# 10. Testar o site localmente
php artisan serve

# Acesse no navegador:
 http://127.0.0.1:8000
