CANAL SOM - CLIENTES v1.0.2

Correções:
- Campo de CEP passa de zip_code para postal_code, conforme a tabela real customers.
- Telefone e WhatsApp passam a maxlength=14 para aceitar (99)99999-9999 completo.
- Controller e Model alinhados ao banco real.
- Tela de detalhes passa a ler postal_code.
- Não altera banco de dados e não cria migration.

Instalação:
1. Backup:
   cd /home/infogate/infogate-gestao
   cp app/Http/Controllers/CustomerController.php app/Http/Controllers/CustomerController.php.bkp_v1_0_2
   cp app/Models/Customer.php app/Models/Customer.php.bkp_v1_0_2
   cp resources/views/customers/_form.blade.php resources/views/customers/_form.blade.php.bkp_v1_0_2
   cp resources/views/customers/show.blade.php resources/views/customers/show.blade.php.bkp_v1_0_2

2. Extraia este ZIP na raiz do projeto.

3. Valide:
   php -l app/Http/Controllers/CustomerController.php
   php -l app/Models/Customer.php

4. Limpe:
   php artisan optimize:clear

5. Teste novo cadastro de cliente.
