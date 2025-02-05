# Teste Técnico FacilConsulta - PHP/Laravel

Nesse repositório contém o teste tecnico da FacilConsulta utilizando Laravel 11

## Configuração do Projeto

1. Clone o repositório:
   ```bash
   git clone https://github.com/vitorvargasdev/teste-tecnico-facilconsulta.git
   cd teste-tecnico-facilconsulta
   ```

2. Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente:
   ```bash
   cp .env.example .env
   ```

3. Suba os containers com o Sail:
   ```bash
   ./vendor/bin/sail up -d
   ```

4. Instale as dependências:
   ```bash
   ./vendor/bin/sail composer install
   ```

5. Gere a chave da aplicação:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. Execute as migrações e seeds:
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

## Executando os Testes

Para garantir a integridade da aplicação, execute os testes com o PHPUnit:

```bash
./vendor/bin/sail artisan test
```

## Cobertura de Testes

A aplicação possui **100% de cobertura de testes**. Abaixo está o relátorio de cobertura:

![image](https://github.com/user-attachments/assets/da65787f-09b2-4541-9971-7649384f8ed2)


Para gerar o relátorio de cobertura:

```bash
./vendor/bin/sail artisan test --coverage
```

## Considerações Finais

Este projeto foi desenvolvido com foco em boas práticas de código, testes automatizados e padrões do Laravel.
