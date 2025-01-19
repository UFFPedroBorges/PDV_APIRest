# PDV_APIRest
Protótipo de um sistema de PDV com EPI Rest implementado.

---------------------------------------------------------------
Guia de Uso da API

Este documento descreve como utilizar a API para gerenciar 
Clientes, Produtos, Títulos, e Vendas. Todos os endpoints 
exigem autenticação através de um token JWT obtido no login.

---------------------------------------------------------------
1. Autenticação

**Endpoint**: POST /api/login.php

**Descrição**: Este endpoint autentica o usuário e retorna um 
token JWT que deve ser usado nos outros endpoints.

**Headers**:
Content-Type: application/json

**Body (JSON)**:

    {
        "email": "admin@loja.com",
        "senha": "senha123"
    }

**Resposta de Sucesso**:

    {
        "mensagem": "Login realizado com sucesso",  
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
    }

**Erros Comuns**:
- 401 Unauthorized: Credenciais inválidas.

---------------------------------------------------------------
2. Clientes

**GET**: /api/clientes.php

**Descrição**: Retorna uma lista de todos os clientes cadastrados 
no sistema.

**Headers**:
Authorization: Bearer <seu_token_jwt>

**Resposta**:

    [
        {
            "idCliente": 1,
            "nome": "João Silva",
            "email": "joao.silva@gmail.com",
            "telefone": "(11) 98765-4321",
            "endereco": "Rua das Flores, 123, São Paulo, SP"
        },
        ...
    ]

**POST**: /api/clientes.php

**Descrição**: Cadastra um novo cliente no sistema.

**Headers**:
Authorization: Bearer <seu_token_jwt>,
Content-Type: application/json

**Body (JSON)**:

    {
        "nome": "Maria Oliveira",
        "email": "maria.oliveira@hotmail.com",
        "telefone": "(21) 99876-5432",
        "endereco": "Avenida Brasil, 456, Rio de Janeiro, RJ"
    }

**Resposta**:

    {
        "mensagem": "Cliente cadastrado com sucesso",
        "idCliente": 2
    }

**Permissão**:
Todos os usuários autenticados podem cadastrar clientes.

---------------------------------------------------------------
3. Produtos

**GET**: /api/produtos.php

**Descrição**: Retorna uma lista de todos os produtos cadastrados 
no sistema.

**Headers**:
Authorization: Bearer <seu_token_jwt>

**Resposta**:

    [
        {
            "idProduto": 1,
            "nome": "Tinta Branca 18L",
            "preco": 150.00,
            "estoque": 40
        },
        ...
    ]

**POST**: /api/produtos.php

**Descrição**: Cadastra um novo produto no sistema.

**Headers**:
Authorization: Bearer <seu_token_jwt>,
Content-Type: application/json

**Body (JSON)**:

    {
        "nome": "Tinta Azul 18L",
        "preco": 160.00,
        "estoque": 30
    }

**Resposta**:

    {
        "mensagem": "Produto cadastrado com sucesso",
        "idProduto": 2
    }

**Permissão**:
Apenas usuários com nível de acesso 1 podem cadastrar produtos.

---------------------------------------------------------------
4. Títulos

**GET**: /api/titulos.php?idCliente={idCliente}  
**Descrição**: Retorna os títulos associados a um cliente.

**GET**: /api/titulos.php?idVenda={idVenda}  
**Descrição**: Retorna os títulos associados a uma venda.

**Headers**:
Authorization: Bearer <seu_token_jwt>

**Resposta**:

    [
        {
            "idTitulo": 1,
            "idVenda": 1,
            "parcela": 1,
            "valorTitulo": 160.00,
            "valorPago": 160.00,
            "dataEmissao": "2025-01-15 10:30:00",
            "dataVencimento": "2025-01-15 00:00:00",
            "dataPago": "2025-01-15 10:30:00"
        },
        ...
    ]

**PUT**: /api/titulos.php?idTitulo={idTitulo}

**Descrição**: Atualiza os dados de um título existente.

**Headers**:
Authorization: Bearer <seu_token_jwt>,
Content-Type: application/json

**Body (JSON)**:
Todos os campos são opcionais.

    {
        "valorPago": 160.00,
        "dataPago": "2025-01-15",
        "dataVencimento": "2025-01-20",
        "valorTitulo": 160.00
    }

**Resposta**:

    {
        "mensagem": "Título atualizado com sucesso",
        "idTitulo": 1
    }

**Permissão**:
Apenas usuários com nível de acesso 1 podem atualizar títulos.

---------------------------------------------------------------
5. Vendas

**GET**: /api/vendas.php?idVenda={idVenda}

**Descrição**: Retorna os detalhes de uma venda.

**Headers**:
Authorization: Bearer <seu_token_jwt>

**Resposta**:

    {
        "idVenda": 1,
        "idUsuario": 2,
        "idCliente": 1,
        "dataVenda": "2025-01-15 10:30:00",
        "valorTotal": 320.00,
        "itens": [
            {
                "idProduto": 1,
                "nome": "Tinta Branca 18L",
                "quantidade": 2,
                "valorUnitario": 150.00
            },
            ...
        ]
    }

**POST**: /api/vendas.php

**Descrição**: Cadastra uma nova venda.

**Headers**:
Authorization: Bearer <seu_token_jwt>,
Content-Type: application/json

**Body (JSON)**:
O campo "titulos" é opcional. Caso omitido, será criada uma 
única parcela com o valor total da venda e vencimento na data atual.

    {
        "idCliente": 1,
        "itens": [
            {
                "idProduto": 1,
                "quantidade": 2,
                "valorUnitario": 150.00
            },
            {
                "idProduto": 2,
                "quantidade": 1,
                "valorUnitario": 100.00
            }
        ],
        "titulos": [
            {
                "valorParcela": 250.00,
                "dataVencimento": "2025-01-22"
            },
            {
                "valorParcela": 200.00,
                "dataVencimento": "2025-01-29"
            }
        ]
    }

**Resposta**:

    {
        "mensagem": "Venda registrada com sucesso",
        "idVenda": 1,
        "valorTotal": 450.00
    }

**Validações**:
- Cliente deve existir.
- Produtos devem existir e ter estoque suficiente.
- Soma das parcelas (se fornecidas) deve ser igual ao valor total da venda.
