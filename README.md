# Instalação
Clone o repositório em seu computador
<pre>
git cone git@github.com:renanjoppert1/innyx-fullstack-developer-test.git
</pre>

Depois de clonar, vamos precisar fazer algumas configurações no nosso projeto.

## Variáveis de Ambiente

Vamos configurar as nossas variáveis de ambiente. 

### Arquivo .env na raiz do projeto
Primeiro passo será copiar o arquivo .env.example para .env na raiz do projeto. 

Neste arquivo, você irá definir os dados de banco de dados. Defina:
<pre>
DB_DATABASE
DB_USERNAME
DB_PASSWORD
</pre>

### Arquivo .env na pasta backend
Copie o arquivo .env.example para .env dentro da pasta backend. Repita as mesmas variáveis de banco de dados definido no passo anterior para este arquivo. 

### Arquivo .env na pasta frontend
Copie o arquivo .env.example para .env dentro da pasta frontend.

## Subindo a Aplicação
Para subir a aplicação utilizando docker, basta no terminal na raiz do projeto executar:
<pre>
docker-compose up -d
</pre>

Após o docker lançar a aplicação, você deverá executar alguns passos:

### Instalar dependências do Laravel
Em seu terminal execute
<pre>
docker-compose exec app composer install
</pre>

### Rodar a seeding no banco de dados
Para popular o banco de dados do projeto, execute em seu terminal:
<pre>
docker-compose exec app php artisan migrate:fresh --seed
</pre>

### Instalar dependências do Frontend
Em seu terminal dentro da pasta frontend execute
<pre>
npm i
</pre>

### Verificando portas
Para verificar as portas que a aplicação está rodando, basta executar no terminal:
<pre>
docker-compose ps
</pre>

Ele irá mostrar algo parecido com:
<pre>
teste-innyx_app_1          docker-php-entrypoint php-fpm    Up      9000/tcp                                             
teste-innyx_mysql_1        docker-entrypoint.sh mysqld      Up      0.0.0.0:32780->3306/tcp,:::32780->3306/tcp, 33060/tcp
teste-innyx_nginx_1        /docker-entrypoint.sh ngin ...   Up      0.0.0.0:32771->80/tcp,:::32771->80/tcp               
teste-innyx_phpmyadmin_1   /docker-entrypoint.sh apac ...   Up      0.0.0.0:32781->80/tcp,:::32781->80/tcp  
</pre>

### Rodando o frontend
Em seu terminal dentro da pasta frontend execute:
<pre>
npm run dev
</pre>


## Documentação da API

Para gerar a documentação da API para uma collection no postman, execute em seu terminal:
<pre>
docker-compose exec app php artisan export:postman
</pre>

A collection será criada e salva na pasta `backed/storage/app/postman` no arquivo collection.json. 

Após realizar a importação faça o login na API para receber o token de autenticação. As credenciais estão fixadas no final da documentação.

Uma vez com acesso ao token, vá nas configurações da collection dentro do postman, em variables e  insira na coluna Current Value o seu token de autenticação. 

Agora você conseguirá fazer qualquer requisição dentro da collection sem precisar inserir o token manualmente em cada requisição. 

## Usuários
<pre>
email => admin@innyx.com
senha => 123456
</pre>

<pre>
email => user@innyx.com
senha => 123456
</pre>

## Libaries Utilizadas
Abaixo segue o motivo de cada lib que foi utilizada além do que já vem do framework usada tando frontend quanto backend

### Backend
andreaselia/laravel-api-to-postman: Biblioteca que gera collection do POSTMAN para as rotas de API de forma simlificada via terminal
guiador-digital/generate-laravel-architecture-copilot: Ferramenta de desenvolvimento próprio que permite criar CRUDs completos com apenas 1 comando

### Frontend
@mdi/font: Carrega a icon font do material design
axios: lib usada para fazer requisições HTTP
material-design-icons-iconfont: utilizada para carregar o icon font do material design também
vue-toastification: lib para disparar toast atrativos visualmente
vuetify: lib para carregar componentes baseados no Material Design
@iconify/vue: lib usada para carregar outros tipos de icones utilizando Iconify
