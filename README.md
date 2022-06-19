# Descrição
Repositório dedicado à solução de um teste de branck-end. Neste respositório foi utilizado o framework Laravel por já possuir diversas ferramentas que facilitam a tratativa de requesições http, comunicação com a camada de dados e toda uma estrutura que padroniza o projeto. 

## Ferramentas utilizadas
- Laravel 9
- Mysql
- Docker
- Swagger

## Instalação

- Instale as dependências do projeto
~~~bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v $(pwd):/var/www/html \
  -w /var/www/html \
  laravelsail/php81-composer:latest \
  composer install --ignore-platform-reqs
~~~

- Configure as variáveis de ambiente, basta apenas rodar o comando abaixo:
~~~bash
cp .env.example .env
~~~

- Inicializar a aplicação
~~~bash
./vendor/bin/sail up -d
~~~

- Popular banco de dados
~~~bash
./vendor/bin/sail artisan migrate
~~~

## Inicializando e parando a aplicação
O Laravel possui uma espécie de wrapper que sob o capô executa o Docker (https://laravel.com/docs/9.x/sail#installation).

- Inicializar a aplicação
~~~bash
./vendor/bin/sail up -d
~~~

- Parar e remover os containers da aplicação
~~~bash
./vendor/bin/sail down
~~~
