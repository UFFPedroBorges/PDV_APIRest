-- pdv.cliente

INSERT INTO cliente (IdCliente, Nome, Email, Telefone, Endereco)
VALUES
(1, 'João Silva', 'joao.silva@gmail.com', '(11) 98765-4321', 'Rua das Flores, 123, São Paulo, SP'),
(2, 'Maria Oliveira', 'maria.oliveira@hotmail.com', '(21) 99876-5432', 'Avenida Brasil, 456, Rio de Janeiro, RJ'),
(3, 'Pedro Santos', 'pedro.santos@yahoo.com', '(31) 91234-5678', 'Rua Minas Gerais, 789, Belo Horizonte, MG');

-- pdv.produto

INSERT INTO produto (IdProduto, Nome, Preco, Estoque)
VALUES
(1, 'Tinta Branca 18L', 150, 40),
(2, 'Tinta Azul 18L', 160, 30),
(3, 'Tinta Vermelha 3.6L', 50, 100),
(4, 'Tinta Verde 3.6L', 55, 80),
(5, 'Rolo de Pintura', 20, 200),
(6, 'Pincel Médio', 10, 150);

-- pdv.usuario

INSERT INTO usuario (IdUsuario, Nome, Email, Senha, NivelAcesso)
VALUES
(1, 'Admin', 'admin@loja.com', 'senha123', 1),
(2, 'Vendedor', 'vendedor@loja.com', 'senha123', 2);

-- pdv.venda

INSERT INTO venda (IdVenda, IdUsuario, IdCliente, DataVenda, ValorTotal)
VALUES
(1, 2, 1, '2025-01-15 10:30:00', 320),
(2, 2, 2, '2025-01-15 11:00:00', 210),
(3, 2, 3, '2025-01-15 12:15:00', 470);

-- pdv.venda_item

INSERT INTO venda_item (IdVendaItem, IdVenda, IdProduto, Quantidade, ValorUnitario)
VALUES
(1, 1, 1, 2, 150),
(2, 1, 5, 1, 20),
(3, 2, 3, 1, 160),
(4, 2, 6, 5, 10),
(5, 3, 3, 5, 50),
(6, 3, 4, 4, 55);

-- pdv.titulo

INSERT INTO titulo (IdTitulo, IdVenda, Parcela, ValorTitulo, ValorPago, DataEmissao, DataVencimento, DataPago)
VALUES
(1, 1, 1, 160, 160, '2025-01-15 10:30:00', '2025-01-15 00:00:00', '2025-01-15 10:30:00'),
(2, 2, 1, 160, NULL, '2025-01-15 10:30:00', '2025-01-22 00:00:00', NULL),
(3, 2, 2, 210, 210, '2025-01-15 11:00:00', '2025-01-15 00:00:00', '2025-01-15 11:00:00'),
(4, 3, 1, 160, 160, '2025-01-15 12:15:00', '2025-01-22 00:00:00', '2025-01-18 11:42:00'),
(5, 3, 2, 160, NULL, '2025-01-15 12:15:00', '2025-02-29 00:00:00', NULL),
(6, 3, 3, 150, NULL, '2025-01-15 12:15:00', '2025-02-05 00:00:00', NULL);