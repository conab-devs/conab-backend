<h1 align="center">Projeto Conab (Backend)</h1>

<img src="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Ftse2.mm.bing.net%2Fth%3Fid%3DOIP.6_lXVWVlBURcXXnrUlpaggHaFj%26pid%3DApi&f=1" />

## Uso

### Pre-requisitos

Para executar a aplicação é necessário ter instalado o ```PHP >= 7.2.5``` e o [Composer](https://getcomposer.org/download/). 
Para um guia completo, por favor siga o [guia de instalação](https://laravel.com/docs/7.x#installation).
  
### Setup

Antes de executar a aplicação é necessário efetuar alguns comandos:

##### Instalar todas as dependências
```shell script
$ composer install
```

##### Faça uma cópia do arquivo .env.example.
```shell script
$ cp .env.example .env
```

##### Gere a chave da aplicação.
```shell script
$ php artisan key:generate
```

##### Gere o secret do JWT
```shell script
$ php artisan jwt:secret
```

##### Configurar o banco de dados

Adicionar as seguintes variaveis de ambiente:

```dotenv
DB_CONNECTION=
DATABASE_URL=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

A configuração dependerá do banco de dados utilizado, por isso para mais informações 
siga o [guia inícial](https://laravel.com/docs/7.x/database).

> Em ambiente de _produção/desenvolvimento_ está sendo utilizado o **Postgres** e para o ambiente de
> _teste_ o **SQLite**.

##### Configurar o STMP

Adicionar as seguintes variaveis de ambiente:

```dotenv
MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=
```

> Em ambiente de desenvolvimento está sendo utilizado o [Mailtrap](https://mailtrap.io/).

O envio de email é feito utilizando QUEUE, por isso para efetuar essa operação é necessário executar o
comando ````php artisan queue:work```` em outro terminal.
 
##### Configurar o Firebase Storage (opcional)

Adicionar as seguintes variaveis de ambiente:

```dotenv
FIREBASE_TYPE=
FIREBASE_PROJECT_ID= 
FIREBASE_PRIVATE_KEY_ID= 
FIREBASE_PRIVATE_KEY= 
FIREBASE_CLIENT_EMAIL=
FIREBASE_CLIENT_ID=
FIREBASE_AUTH_URI=
FIREBASE_TOKEN_URI=
FIREBASE_AUTH_PROVIDER_X509_CERT_URL=
FIREBASE_CLIENT_X509_CERT_URL=
```

> Em ambiente de desenvolviemnto/teste é utilizado o local storage.

##### Efetuar a migração

```shell script
$ php artisan migrate
```

##### Efetuar a seed

Esse comando adicionará um usuário com email **"adminconab@email.com"** e senha **"123456"**.

```shell script
$ php artisan db:seed
```

### Execução

##### Inicializar o servidor

```shell script
$ php artisan serve
```

##### Listar todas as rotas

```shell script
$ php artisan route:list
```

##### Executar os testes

```shell script
$ php artisan test
```

### Outras informações

Mais informações podem ser encontradas na [documentação](https://laravel.com/docs/7.x) do laravel. 

> Lembrando que esse projeto está utilizando a versão **7.x** do framework.



