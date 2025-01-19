-- pdv.cliente definition

CREATE TABLE `cliente` (
  `IdCliente` int(11) NOT NULL AUTO_INCREMENT,
  `Nome` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Telefone` varchar(20) DEFAULT NULL,
  `Endereco` text,
  PRIMARY KEY (`IdCliente`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


-- pdv.produto definition

CREATE TABLE `produto` (
  `IdProduto` int(11) NOT NULL AUTO_INCREMENT,
  `Nome` varchar(100) NOT NULL,
  `Preco` decimal(10,2) NOT NULL,
  `Estoque` int(11) NOT NULL,
  PRIMARY KEY (`IdProduto`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;


-- pdv.usuario definition

CREATE TABLE `usuario` (
  `IdUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `Nome` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Senha` varchar(255) NOT NULL,
  `NivelAcesso` int(11) NOT NULL,
  PRIMARY KEY (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


-- pdv.venda definition

CREATE TABLE `venda` (
  `IdVenda` int(11) NOT NULL AUTO_INCREMENT,
  `IdUsuario` int(11) NOT NULL,
  `IdCliente` int(11) NOT NULL,
  `DataVenda` datetime NOT NULL,
  `ValorTotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`IdVenda`),
  KEY `IdUsuario` (`IdUsuario`),
  KEY `IdCliente` (`IdCliente`),
  CONSTRAINT `venda_ibfk_1` FOREIGN KEY (`IdUsuario`) REFERENCES `usuario` (`IdUsuario`),
  CONSTRAINT `venda_ibfk_2` FOREIGN KEY (`IdCliente`) REFERENCES `cliente` (`IdCliente`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


-- pdv.venda_item definition

CREATE TABLE `venda_item` (
  `IdVendaItem` int(11) NOT NULL AUTO_INCREMENT,
  `IdVenda` int(11) NOT NULL,
  `IdProduto` int(11) NOT NULL,
  `Quantidade` int(11) NOT NULL,
  `ValorUnitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`IdVendaItem`),
  KEY `IdVenda` (`IdVenda`),
  KEY `IdProduto` (`IdProduto`),
  CONSTRAINT `venda_item_ibfk_1` FOREIGN KEY (`IdVenda`) REFERENCES `venda` (`IdVenda`),
  CONSTRAINT `venda_item_ibfk_2` FOREIGN KEY (`IdProduto`) REFERENCES `produto` (`IdProduto`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;


-- pdv.titulo definition

CREATE TABLE `titulo` (
  `IdTitulo` int(11) NOT NULL AUTO_INCREMENT,
  `IdVenda` int(11) NOT NULL,
  `Parcela` int(11) NOT NULL,
  `ValorTitulo` decimal(10,2) NOT NULL,
  `ValorPago` decimal(10,2) DEFAULT NULL,
  `DataEmissao` datetime NOT NULL,
  `DataVencimento` datetime NOT NULL,
  `DataPago` datetime DEFAULT NULL,
  PRIMARY KEY (`IdTitulo`),
  KEY `IdVenda` (`IdVenda`),
  CONSTRAINT `titulo_ibfk_1` FOREIGN KEY (`IdVenda`) REFERENCES `venda` (`IdVenda`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;