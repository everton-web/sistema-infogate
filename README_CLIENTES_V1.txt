CANAL SOM - CLIENTES V1

Incluído:
- CustomerController com escopo por empresa
- Model Customer completo
- Listagem e busca
- Cadastro PF/PJ
- CPF/CNPJ com máscara
- Telefone e WhatsApp no padrão (99)99999-9999
- E-mail
- Endereço
- Situação Ativo/Inativo
- Tela de detalhes
- Edição
- Veículos vinculados
- Sidebar com Clientes ativado

NÃO altera banco de dados.
A tabela customers já existente é utilizada.

INSTALAÇÃO
==========
1. Faça backup:
   cd /home/infogate/infogate-gestao
   cp app/Models/Customer.php app/Models/Customer.php.bkp_clientes_v1
   cp resources/views/partials/erp-sidebar.blade.php resources/views/partials/erp-sidebar.blade.php.bkp_clientes_v1

2. Extraia o ZIP na raiz:
   /home/infogate/infogate-gestao

3. Edite routes/web.php conforme:
   ROTAS_CLIENTES.txt

4. Valide:
   php -l app/Http/Controllers/CustomerController.php
   php -l app/Models/Customer.php
   php -l routes/web.php

5. Confira rotas:
   php artisan route:list --name=customers

6. Limpe cache:
   php artisan optimize:clear

7. Acesse:
   https://gestao.infogate.com.br/cadastros/clientes

8. Opcional:
   faça o ajuste descrito em AJUSTE_VEICULO_CLIENTE.txt
   para o botão "+ Novo veículo" já abrir com o cliente selecionado.

Não rode migrate:fresh e não apague tabelas.
