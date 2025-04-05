# Top 5 musicas Tião Carreiro & Pardinho
Pagina web que exibe uma lista das 5 musicas mais tocadas da dupla caipira Tião Carreiro e Pardinho e permite que o usuário sugira novas musicas informando um link válido no YouTube.

### Esse é o back-end do projeto, para ter acesso ao Front [clique aqui](https://github.com/CandidoRPNeto/top-five-songs-front)
### E para ter acesso a documentação que fez esse projeto ser concluido [clique aqui](https://docs.google.com/document/d/1vACHjs0kJnu2AlwZyGqdHVVEOsl0eEGDQH5MzY7Pfy4/edit?usp=sharing)

## Configuração e Execução

requisitos 
- php >= 8.2
- docker
- git

colone o repositorio via Https ou SSH ( [siga esse tutorial](https://docs.github.com/en/authentication/connecting-to-github-with-ssh/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent) )

## Com o repositório clonado execute os seguinte passos:

### 1 - Execute o docker do sistema com o banco de dados local
```bash
./vendor/bin/sail up -d
```

### 2 - Instale as dependências do projeto
```bash
 composer install
```

### 3 - Crie um .env baseado no exemplo
```bash
 cp .env.example .env 
```

### 4 - Gere as chaves para o banco e para o jwt
```bash
 php artisan key:generate 
```
```bash
 php artisan jwt:secret 
```

### 5 - rode as migrations e seeders
```bash
 php artisan migrate --seed
```
