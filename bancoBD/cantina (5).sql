-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 15/05/2025 às 19:34
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cantina`
--
CREATE DATABASE IF NOT EXISTS `cantina` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cantina`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nome_cliente` varchar(40) NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `numero_tel_cliente` varchar(9) NOT NULL,
  `senha` varchar(40) DEFAULT NULL,
  `instituicao` varchar(100) DEFAULT 'Senai Joinville Sul'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nome_cliente`, `cpf`, `numero_tel_cliente`, `senha`, `instituicao`) VALUES
(1, 'João Silva', '123.456.789-00', '999999999', '45667865', 'Senai Joinville Sul'),
(3, 'Carlos Souza', '456.789.123-00', '977777777', '5656865', 'Senai Joinville Sul'),
(4, 'Ana Costa', '789.123.456-00', '966666666', '45676788', 'Senai Joinville Sul'),
(5, 'Pedro Lima', '321.654.987-00', '955555555', '455667', 'Senai Joinville Sul'),
(6, 'Fernanda Alves', '654.987.321-00', '944444444', '567786', 'Senai Joinville Sul'),
(7, 'Lucas Pereira', '159.753.486-00', '933333333', '4576875', 'Senai Joinville Sul'),
(8, 'Juliana Rocha', '357.951.258-00', '922222222', '5567654', 'Senai Joinville Sul'),
(9, 'Ricardo Santos', '258.159.753-00', '911111111', '765434', 'Senai Joinville Sul'),
(10, 'Patrícia Mendes', '951.357.852-00', '900000000', '2345', 'Senai Joinville Sul'),
(11, 'Alice Sangalli Pereira', '327.589.124-36', '119478523', 'alice123', 'Senai Joinville Sul'),
(12, 'Amanda Reis Carvalho', '854.216.937-48', '219653287', 'amanda456', 'Senai Joinville Sul'),
(13, 'Ana Luiza Luciano Costa', '142.698.573-25', '319874562', 'ana789', 'Senai Joinville Sul'),
(14, 'Ana Maria Vavassori', '963.574.821-59', '419123658', 'ana234', 'Senai Joinville Sul'),
(15, 'Ananda Gabriely Alves', '745.289.631-74', '519569841', 'ananda567', 'Senai Joinville Sul'),
(16, 'Anna Julia Soares', '582.136.947-26', '619741259', 'anna890', 'Senai Joinville Sul'),
(17, 'Arthur Antonio Fernandes', '319.768.245-83', '719485231', 'arthur345', 'Senai Joinville Sul'),
(18, 'Cristyan Egner', '678.942.315-68', '819362178', 'cristyan678', 'Senai Joinville Sul'),
(19, 'Eduardo Sedrez Da Silva', '852.314.769-21', '919574123', 'eduardo901', 'Senai Joinville Sul'),
(20, 'Emilly Da Silva Santana', '123.678.954-87', '319265814', 'emilly234', 'Senai Joinville Sul'),
(21, 'Gabriel Pires Dunzer', '951.247.836-59', '419687432', 'gabriel567', 'Senai Joinville Sul'),
(22, 'Gustavo Da Silva Camargo', '753.962.481-35', '519471582', 'gustavo890', 'Senai Joinville Sul'),
(23, 'Gustavo Joaquim Ferreira do Vale', '369.741.852-69', '619758621', 'gustavo123', 'Senai Joinville Sul'),
(24, 'Gustavo Luciano Brunken', '987.126.453-97', '719235847', 'gustavo456', 'Senai Joinville Sul'),
(25, 'Gustavo Zimermann Lopes Barroso', '478.563.219-48', '819147536', 'gustavo789', 'Senai Joinville Sul'),
(26, 'João Paulo Amaral', '842.651.973-75', '919856421', 'joão234', 'Senai Joinville Sul'),
(27, 'Jonathan Redmerski Kalinoski', '236.987.451-86', '319521468', 'jonathan567', 'Senai Joinville Sul'),
(28, 'Marco Antonio Hoffer Madruga Filho', '741.369.852-14', '419612537', 'marco890', 'Senai Joinville Sul'),
(29, 'Maria Gabriela Massignan', '963.258.147-92', '519473625', 'maria345', 'Senai Joinville Sul'),
(30, 'Marlon Brendon Pellense', '125.846.739-57', '619382574', 'marlon678', 'Senai Joinville Sul'),
(31, 'Matheus Esboinski Siewes', '587.124.963-84', '119523687', 'matheus901', 'Senai Joinville Sul'),
(32, 'Matheus Henrique Andrade Da Silva', '396.548.217-23', '219632157', 'matheus234', 'Senai Joinville Sul'),
(33, 'Matheus Henrique Vieira', '845.692.317-45', '319856321', 'matheus567', 'Senai Joinville Sul'),
(34, 'Matheus Marques Costa', '214.786.953-68', '419741253', 'matheus890', 'Senai Joinville Sul'),
(35, 'Matheus Pinheiro', '897.452.316-74', '519256387', 'matheus123', 'Senai Joinville Sul'),
(36, 'Nathalia da Silva', '365.987.241-35', '619175623', 'nathalia456', 'Senai Joinville Sul'),
(37, 'Noah Kruger Nunes Lopes', '741.258.963-12', '719584712', 'noah789', 'Senai Joinville Sul'),
(38, 'Pedro Henrique Correa Cardoso', '523.896.741-86', '819874156', 'pedro234', 'Senai Joinville Sul'),
(39, 'Pedro Henrique Franco Dos Santos', '968.471.523-54', '919356287', 'pedro567', 'Senai Joinville Sul'),
(40, 'Rafaelly Cassiane Pedro Da Silva', '847.159.326-45', '319247863', 'rafaelly890', 'Senai Joinville Sul'),
(41, 'Rogerio dos Santos', '365.214.897-25', '419132587', 'rogerio345', 'Senai Joinville Sul'),
(42, 'Ruan Figueiredo Corrêa', '987.123.654-78', '519748563', 'ruan678', 'Senai Joinville Sul'),
(43, 'Samuel Haag Reginaldo', '658.321.947-51', '619258416', 'samuel901', 'Senai Joinville Sul'),
(44, 'Victoria Gabrieli da Luz', '471.569.832-69', '719784132', 'victoria234', 'Senai Joinville Sul'),
(45, 'Vitor Paludo', '213.456.789-32', '819456783', 'vitor567', 'Senai Joinville Sul'),
(46, 'Hugo Menezes Barra', '325.874.961-45', '119874563', 'hugo890', 'Senai Joinville Sul'),
(47, 'Kamylla Costa Abd El Jawad', '874.125.693-78', '219563214', 'kamylla123', 'Senai Joinville Sul'),
(48, 'Marcio José Kams Senhorinha', '698.745.231-65', '319874125', 'marcio456', 'Senai Joinville Sul'),
(49, 'Jarbas Jose de Araujo', '523.698.147-95', '419652314', 'jarbas789', 'Senai Joinville Sul'),
(50, 'Bruno Pedroso Lima Silva', '741.258.369-82', '519874563', 'bruno234', 'Senai Joinville Sul'),
(51, 'Vitor Pruss', '369.852.147-26', '619785412', 'vitor567', 'Senai Joinville Sul');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `data_pedido` datetime DEFAULT current_timestamp(),
  `status` enum('novo','pendente','pago','retirado') NOT NULL DEFAULT 'novo',
  `total` decimal(10,2) NOT NULL,
  `unidade` varchar(100) DEFAULT NULL,
  `forma_pagamento` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_cliente`, `data_pedido`, `status`, `total`, `unidade`, `forma_pagamento`) VALUES
(156, 15, '2025-04-16 16:49:09', 'retirado', 13.00, 'Unidade Sul', 'pix'),
(157, 12, '2025-04-16 16:50:18', 'pago', 23.00, 'Unidade Sul', 'cartao'),
(158, 22, '2025-04-16 16:50:45', 'retirado', 10.00, 'Unidade Sul', 'pix'),
(159, 25, '2025-04-16 16:51:23', 'pago', 26.00, 'Unidade Sul', 'pix'),
(160, 49, '2025-04-16 16:52:19', 'pago', 12.00, 'Unidade Sul', 'cartao'),
(161, 51, '2025-04-16 16:52:56', 'pago', 17.00, 'Unidade Sul', 'pix'),
(162, 48, '2025-04-16 16:53:26', 'pago', 21.00, 'Unidade Sul', 'pix'),
(163, 37, '2025-04-16 16:54:07', 'novo', 19.00, 'Unidade Sul', 'pix'),
(164, 29, '2025-04-16 16:54:56', 'retirado', 17.00, 'Unidade Sul', 'pix'),
(165, 30, '2025-04-27 13:33:13', 'pago', 27.00, 'Unidade Sul', 'pix'),
(166, 30, '2025-04-27 13:34:31', 'pago', 18.00, 'Unidade Sul', 'pix'),
(167, 30, '2025-04-27 13:35:03', 'pago', 18.00, 'Unidade Norte', 'pix'),
(168, 30, '2025-04-27 13:51:22', 'pago', 9.00, 'Unidade Norte', 'pix'),
(169, 30, '2025-04-30 14:17:41', 'pago', 12.50, 'Unidade Sul', 'pix'),
(170, 30, '2025-04-30 14:51:19', 'pago', 90.00, 'Unidade Sul', 'pix'),
(171, 30, '2025-04-30 16:21:46', 'pago', 70.00, 'Unidade Norte', 'pix'),
(172, 30, '2025-04-30 16:21:54', 'pago', 42.00, 'Unidade Sul', 'pix'),
(173, 30, '2025-03-27 16:49:09', 'pago', 100.00, 'Unidade Sul', 'pix'),
(174, 30, '2025-05-05 13:46:54', 'retirado', 46.00, 'Unidade Sul', 'pix'),
(175, 30, '2025-05-12 16:23:17', 'pago', 25.00, 'Unidade Sul', 'pix'),
(176, 30, '2025-05-12 16:34:40', 'pago', 6.00, 'Unidade Sul', 'pix'),
(177, 30, '2025-05-12 16:45:21', 'pago', 9.00, 'Unidade Sul', 'cartao'),
(178, 30, '2025-05-14 14:44:01', 'pago', 9.00, 'Unidade Leste', 'pix');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto`
--

CREATE TABLE `produto` (
  `id_produto` int(11) NOT NULL,
  `valor_compra` varchar(30) NOT NULL,
  `valor_venda` decimal(10,2) NOT NULL,
  `qtde` varchar(20) NOT NULL,
  `nome_produto` varchar(40) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `categoria` enum('Doces','Salgados','Bebidas','Combos','Promoção da Semana') NOT NULL DEFAULT 'Doces',
  `promocao` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `valor_promocional` decimal(10,2) DEFAULT NULL,
  `stat` varchar(7) DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto`
--

INSERT INTO `produto` (`id_produto`, `valor_compra`, `valor_venda`, `qtde`, `nome_produto`, `imagem`, `categoria`, `promocao`, `valor_promocional`, `stat`) VALUES
(3, '5', 7.00, '47', 'Sonho de Creme', 'uploads/sonhodecreme.png', 'Doces', 50, 3.50, 'Ativo'),
(4, '2', 10.00, '41', 'Torta de Frango', 'uploads\\tortadefrango.png', 'Promoção da Semana', 10, 9.00, 'Ativo'),
(6, '5.00', 6.00, '48', 'Torta de Morango', 'uploads/tortinhademorango.png', 'Doces', 0, NULL, 'Ativo'),
(7, '6.00', 7.00, '55', 'Bolo de Cenoura', 'uploads/bolodecenoura.png', 'Doces', 0, NULL, 'Ativo'),
(8, '2.00', 8.00, '59', 'Bolo de Chocolate', 'uploads/bolodechocolate.png', 'Doces', 0, NULL, 'Ativo'),
(10, '6.00', 5.00, '64', 'Pudim de Leite Condensado', 'uploads/pudimltcondensado.png', 'Doces', 0, NULL, 'Ativo'),
(11, '2.00', 6.00, '59', 'Quindim', 'uploads/quindim.png', 'Doces', 0, NULL, 'Ativo'),
(12, '3', 7.00, '50', 'Pastel de Creme', 'uploads/pasteldecreme.png', 'Salgados', 0, NULL, 'Ativo'),
(13, '4.00', 8.00, '67', 'Churros de Doce de Leite', 'uploads/churrosdedocedeleite.png', 'Doces', 0, NULL, 'Ativo'),
(14, '5.00', 9.00, '58', 'Churros de Chocolate', 'uploads\\churrosdechocolate.png', 'Doces', 0, NULL, 'Ativo'),
(15, '3', 5.00, '71', 'Empada de Frango', 'uploads\\empadadefrango.png', 'Salgados', 0, NULL, 'Ativo'),
(16, '5', 7.00, '72', 'Pastel Assado de Carne', 'uploads\\pastelassadodecarne.png', 'Salgados', 0, NULL, 'Ativo'),
(17, '6', 8.00, '73', 'Pastel Assado de Queijo', 'uploads\\pastelassadoqueijo.png', 'Salgados', 0, NULL, 'Ativo'),
(18, '2', 9.00, '66', 'Pastel Assado de Frango', 'uploads\\pastelassadofrango.png', 'Salgados', 0, NULL, 'Ativo'),
(19, '3.00', 10.00, '75', 'Torta Holandesa', 'uploads\\tortaholandesa.png', 'Doces', 0, NULL, 'Ativo'),
(20, '4', 11.00, '76', 'Torta de Limão', 'uploads\\tortadelimao.png', 'Promoção da Semana', 20, 8.80, 'Ativo'),
(21, '6.00', 6.00, '78', 'Palha Italiana', 'uploads\\palhaitaliana.png', 'Doces', 0, NULL, 'Ativo'),
(22, '2.00', 7.00, '49', 'Brownie', 'uploads\\brownie.png', 'Doces', 0, NULL, 'Ativo'),
(24, '5.00', 10.00, '53', 'Mil Folhas de Creme', 'uploads\\milfolhasdecreme.png', 'Doces', 0, NULL, 'Ativo'),
(27, '5.00', 8.00, '57', 'Bomba de Chocolate', 'uploads\\bombadechocolate.png', 'Doces', 0, NULL, 'Ativo'),
(29, '2.00', 10.00, '60', 'Strudel de Maçã', 'uploads\\strudeldemaca.png', 'Doces', 0, NULL, 'Ativo'),
(34, '4.00', 10.00, '67', 'Rocambole', 'uploads\\rocambole.png', 'Doces', 0, NULL, 'Ativo'),
(36, '4.00', 8.00, '71', 'Bolo de milho', 'uploads\\bolodemilho.png', 'Doces', 0, NULL, 'Ativo'),
(38, '2.00', 11.00, '75', 'Cannoli de Creme', 'uploads\\cannolidecreme.png', 'Doces', 0, NULL, 'Ativo'),
(39, '3.00', 5.00, '75', 'Cone Trufado', 'uploads\\conetrufado.png', 'Doces', 0, NULL, 'Ativo'),
(40, '5.00', 7.00, '78', 'Cone de Maracujá', 'uploads\\conedemaracuja.png', 'Doces', 0, NULL, 'Ativo'),
(41, '6.00', 8.00, '79', 'Pão de Gengibre', 'uploads\\paodegengibre.png', 'Salgados', 0, NULL, 'Ativo'),
(43, '4.00', 11.00, '52', 'Chocotone', 'uploads\\chocotone.png', 'Doces', 0, NULL, 'Ativo'),
(44, '5.00', 5.00, '53', 'Alfajor Tradicional', 'uploads\\alfajor.png', 'Doces', 0, NULL, 'Ativo'),
(45, '4.00', 9.00, '57', 'Madeleine', 'uploads\\madeleine.png', 'Doces', 0, NULL, 'Ativo'),
(46, '6.00', 11.00, '59', 'Eclair de Café', 'uploads\\eclair.png', 'Doces', 0, NULL, 'Ativo'),
(47, '2.00', 5.00, '60', 'Brioche de Chocolate', 'uploads\\briochedechocolate.png', 'Doces', 0, NULL, 'Ativo'),
(48, '3.00', 6.00, '61', 'Pão de Batata Recheado', 'uploads\\paobatatarecheado.png', 'Salgados', 0, NULL, 'Ativo'),
(50, '3.00', 11.00, '66', 'Tapioca', 'uploads\\tapioca.png', 'Doces', 0, NULL, 'Ativo'),
(51, '4.00', 5.00, '67', 'Cuca de Banana', 'uploads\\cucadebanana.png', 'Doces', 0, NULL, 'Ativo'),
(52, '6.00', 7.00, '69', 'Banoffee', 'uploads\\banoffe.png', 'Doces', 0, NULL, 'Ativo'),
(56, '2.00', 6.00, '75', 'Petit Four de Goiabada', 'uploads\\petitfourdegoiabada.png', 'Doces', 0, NULL, 'Ativo'),
(57, '3.00', 5.00, '51', 'Pão Australiano', 'uploads\\paoaustraliano.png', 'Salgados', 0, NULL, 'Ativo'),
(58, '4.00', 6.00, '52', 'Pão de Batata Doce', 'uploads\\paobatatadoce.png', 'Salgados', 0, NULL, 'Ativo'),
(59, '6.00', 8.00, '54', 'Pão de Nozes', 'uploads\\paodenozes.png', 'Salgados', 0, NULL, 'Ativo'),
(60, '2.00', 9.00, '55', 'Pão Integral com Castanhas', 'uploads\\paointcomcastanhas.png', 'Salgados', 0, NULL, 'Ativo'),
(61, '5.00', 5.00, '58', 'Bolo de Laranja', 'uploads\\bolodelaranja.png', 'Doces', 0, NULL, 'Ativo'),
(62, '6.00', 6.00, '59', 'Bolo de Abacaxi', 'uploads\\bolodeabacaxi.png', 'Doces', 0, NULL, 'Ativo'),
(63, '4.00', 9.00, '62', 'Pão Sírio', 'uploads\\paosirio.png', 'Salgados', 0, NULL, 'Ativo'),
(64, '2.00', 5.00, '65', 'Pão Francês Especial', 'uploads\\paofrances.png', 'Salgados', 0, NULL, 'Ativo'),
(65, '3.00', 6.00, '66', 'Pão Brioche', 'uploads\\paobrioche.png', 'Salgados', 0, NULL, 'Ativo'),
(66, '5', 10.00, '64', 'Coquetel de Pão de Queijo', 'uploads\\paodequeijo.png', 'Promoção da Semana', 10, 9.00, 'Ativo'),
(68, '4.00', 5.00, '72', 'Pão de Ervas Finas', 'uploads\\paodeervas.png', 'Salgados', 0, NULL, 'Ativo'),
(70, '2.00', 8.00, '75', 'Bolo de Fubá Cremoso', 'uploads\\bolodefubacremoso.png', 'Doces', 0, NULL, 'Ativo'),
(71, '6.00', 10.00, '54', 'Baguete Artesanal', 'uploads\\bagget.png', 'Salgados', 0, NULL, 'Ativo'),
(74, '4.00', 11.00, '61', 'Donuts de Chocolate', 'uploads\\dunutschocolate.png', 'Doces', 0, NULL, 'Ativo'),
(75, '5.00', 5.00, '63', 'Donuts de Ninho', 'uploads\\donutsninho.png', 'Doces', 0, NULL, 'Ativo'),
(76, '6.00', 6.00, '64', 'Donuts de Creme', 'uploads\\donutsdecreme.png', 'Doces', 0, NULL, 'Ativo'),
(77, '3.00', 8.00, '66', 'Tortinha de Maçã', 'uploads\\tortinhademaca.png', 'Doces', 0, NULL, 'Ativo'),
(78, '8', 12.00, '30', 'Suco de Laranja Integral - Natural One', 'uploads/67e6f92611e6d.png', 'Bebidas', 0, NULL, 'Ativo'),
(79, '4.5', 6.00, '30', 'Coca-Cola Lata', 'uploads/67ffeb1f75ec3.png', 'Bebidas', 0, NULL, 'Ativo'),
(80, '4.50', 6.00, '30', 'Coca-Cola Zero Lata', 'uploads\\cocacola0.png', 'Bebidas', 0, NULL, 'Ativo'),
(81, '4.50', 6.00, '17', 'Guaraná Lata', 'uploads\\guarana.png', 'Bebidas', 0, NULL, 'Ativo'),
(82, '1.50', 3.00, '50', 'Água Sem Gás Crystal', 'uploads\\agua.png', 'Bebidas', 0, NULL, 'Ativo'),
(83, '1.89', 3.50, '24', 'Água Com Gás Crystal', 'uploads\\aguagas.png', 'Bebidas', 0, NULL, 'Ativo'),
(84, '2.50', 4.00, '98', 'Matte Leão', 'uploads\\matte.png', 'Bebidas', 0, NULL, 'Ativo'),
(85, '2.90', 4.80, '20', 'Sprite Lata', 'uploads\\sprite.png', 'Bebidas', 0, NULL, 'Ativo'),
(86, '9.00', 12.50, '16', 'Coca-Cola 1L', 'uploads\\coca1.png', 'Bebidas', 0, NULL, 'Ativo'),
(87, '7.50', 10.00, '26', 'Coca-Cola 600ml', 'uploads\\coca600.png', 'Bebidas', 0, NULL, 'Ativo'),
(88, '10', 12.00, '19', 'Pastel + Suco - Combo', 'uploads/combo.png', 'Combos', 0, NULL, 'Ativo'),
(89, '10', 12.00, '34', 'HotDog + Refrigerante - Combo', 'uploads/combo (1).png', 'Combos', 0, NULL, 'Ativo'),
(90, '8', 10.00, '14', 'Bolo + café - Combo', 'uploads/combo (2).png', 'Combos', 0, NULL, 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorios`
--

CREATE TABLE `relatorios` (
  `id_relatorio` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `ano` int(11) NOT NULL,
  `vendidos_mes` int(11) NOT NULL,
  `total_vendas` decimal(10,2) NOT NULL,
  `produto_mais_vendido` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `relatorios`
--

INSERT INTO `relatorios` (`id_relatorio`, `mes`, `ano`, `vendidos_mes`, `total_vendas`, `produto_mais_vendido`) VALUES
(68, 4, 2025, 28, 444.50, 'Pastel de Creme'),
(69, 3, 2025, 10, 100.00, 'Torta Holandesa'),
(70, 5, 2025, 7, 77.00, 'Quindim');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `senha` varchar(10) NOT NULL,
  `cpf` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `senha`, `cpf`) VALUES
(4, 'Alice Silva', 'abcd', '123.456.789-01'),
(5, 'Bruno Oliveira', '5678', '234.567.890-12'),
(6, 'Camila Santos', '9101', '345.678.901-23'),
(7, 'Daniel Costa', '1122', '456.789.012-34'),
(8, 'Eduarda Lima', '3345', '567.890.123-45'),
(9, 'Felipe Rocha', '5567', '678.901.234-56'),
(10, 'Gabriela Souza', '7890', '789.012.345-67'),
(11, 'Henrique Almeida', '2345', '890.123.456-78'),
(12, 'Isabela Martins', '6789', '901.234.567-89'),
(13, 'João Fernandes', '3456', '012.345.678-90'),
(14, 'Karina Mendes', '7891', '123.456.789-02'),
(15, 'Lucas Ribeiro', '2346', '234.567.890-23'),
(16, 'Mariana Cardoso', '6780', '345.678.901-34'),
(17, 'Nathan Azevedo', '3457', '456.789.012-45'),
(18, 'Olívia Duarte', '7892', '567.890.123-56'),
(19, 'Pedro Vasconcelos', '1235', '678.901.234-67'),
(20, 'Quezia Farias', '5679', '789.012.345-78'),
(21, 'Rafael Teixeira', '9102', '890.123.456-89'),
(22, 'Sara Nascimento', '2347', '901.234.567-90'),
(23, 'Thiago Moreira', '6781', '012.345.678-12'),
(24, 'Ursula Freitas', '3458', '123.456.789-03'),
(25, 'Vinícius Cunha', '7893', '234.567.890-34'),
(26, 'Wesley Barbosa', '1236', '345.678.901-45'),
(27, 'Xavier Monteiro', '5670', '456.789.012-56'),
(28, 'Yasmin Batista', '9103', '567.890.123-67'),
(29, 'Zé Augusto', '2348', '678.901.234-78'),
(30, 'Amanda Lopes', '6782', '789.012.345-89'),
(31, 'Breno Araújo', '3459', '890.123.456-90'),
(32, 'Clara Assis', '7894', '901.234.567-01'),
(33, 'Diego Castro', '1237', '012.345.678-23'),
(34, 'Elisa Medeiros', '5671', '123.456.789-04'),
(35, 'Fernando Prado', '9104', '234.567.890-45'),
(36, 'Giovana Peixoto', '2349', '345.678.901-56'),
(37, 'Hugo Rezende', '6783', '456.789.012-67'),
(38, 'Irene Coelho', '3450', '567.890.123-78'),
(39, 'José Henrique', '7895', '678.901.234-89'),
(40, 'Karen Trindade', '1238', '789.012.345-90'),
(41, 'Leonardo Moura', '5672', '890.123.456-01'),
(42, 'Milena Braga', '9105', '901.234.567-12'),
(43, 'Nicolas Andrade', '2340', '012.345.678-34'),
(44, 'Otávio Carvalho', '6784', '123.456.789-05'),
(45, 'Patrícia Leal', '3451', '234.567.890-56'),
(46, 'Rodrigo Figueiredo', '7896', '345.678.901-67'),
(47, 'Sabrina Pires', '1239', '456.789.012-78'),
(48, 'Tadeu Guimarães', '5673', '567.890.123-89'),
(49, 'Uelber Sanches', '9106', '678.901.234-90'),
(50, 'Valentina Mello', '2341', '789.012.345-01'),
(51, 'Wellington Ramos', '6785', '890.123.456-12'),
(52, 'Ximena Vasques', '3452', '901.234.567-23'),
(53, 'Yuri Sena', '7897', '012.345.678-45'),
(54, 'Zuleika Amaral', '1230', '123.456.789-06'),
(55, 'Arthur Pimentel', '5674', '234.567.890-67'),
(56, 'Beatriz Nóbrega', '9107', '345.678.901-78'),
(57, 'César Barreto', '2342', '456.789.012-89'),
(58, 'Daniele Silveira', '6786', '567.890.123-90'),
(59, 'Enzo Sales', '3453', '678.901.234-01'),
(60, 'Fabiana Maciel', '7898', '789.012.345-12'),
(61, 'Gustavo Borges', '1231', '890.123.456-23'),
(62, 'Helena Siqueira', '5675', '901.234.567-34'),
(63, 'Igor Barreiras', '9108', '012.345.678-56'),
(64, 'Juliana Correia', '2343', '123.456.789-07'),
(65, 'Kleber Vieira', '6787', '234.567.890-78'),
(66, 'Lorena Furtado', '3454', '345.678.901-89'),
(67, 'Matheus Arruda', '7899', '456.789.012-90'),
(68, 'Nayara Mourão', '1232', '567.890.123-01'),
(69, 'Orlando Camargo', '5676', '678.901.234-12'),
(70, 'Paloma Rocha', '9109', '789.012.345-23'),
(71, 'Renato Esteves', '2344', '890.123.456-34'),
(72, 'Simone Rangel', '6788', '901.234.567-45'),
(73, 'Tiago Damasceno', '3455', '012.345.678-67'),
(74, 'Ulisses Ferreira', '7890', '123.456.789-08'),
(75, 'Vanessa Braga', '1233', '234.567.890-89'),
(76, 'William Dornelles', '5677', '345.678.901-90'),
(77, 'Xena Duarte', '9100', '456.789.012-01'),
(78, 'Yago Moreira', '2345', '567.890.123-12'),
(79, 'Zilda Barbosa', '6789', '678.901.234-23'),
(80, 'Aline Fernandes', '3456', '789.012.345-34'),
(81, 'Brayan Mendonça', '7891', '890.123.456-45'),
(82, 'Cíntia Ribeiro', '1234', '901.234.567-56'),
(83, 'Denis Teixeira', '5678', '012.345.678-78'),
(84, 'Emanuelle Matos', '9101', '123.456.789-09'),
(85, 'Fábio Neres', '2346', '234.567.890-90'),
(86, 'Graziella Mota', '6780', '345.678.901-01'),
(87, 'Hélio França', '3457', '456.789.012-12'),
(88, 'Isis Neves', '7892', '567.890.123-23'),
(89, 'Jorge Camilo', '1235', '678.901.234-34'),
(90, 'Kelly Magalhães', '5679', '789.012.345-45'),
(91, 'Leonor Aguiar', '9102', '890.123.456-56'),
(92, 'Maurício Paiva', '2347', '901.234.567-67'),
(93, 'Nadja Brito', '6781', '012.345.678-90'),
(94, 'Otacília Luz', '3458', '123.456.789-10'),
(95, 'Pedro Dantas', '7893', '234.567.890-12'),
(96, 'Rita Camacho', '1236', '345.678.901-23'),
(97, 'Silas Xavier', '5670', '456.789.012-34'),
(98, 'Tânia Campos', '9103', '567.890.123-45'),
(99, 'Ubiratan Lopes', '2348', '678.901.234-56'),
(100, 'Vitória Sampaio', '6782', '789.012.345-67'),
(101, 'Washington Cordeiro', '3459', '890.123.456-78'),
(102, 'Xuxa Nascimento', '7894', '901.234.567-89'),
(103, 'Yasmim Fonseca', '1237', '012.345.678-01'),
(104, 'Ziraldo Fernandes', '5671', '123.456.789-11'),
(106, 'Bruno Silva', '5678', '234.567.890-12'),
(107, 'Clara Mendes', '9101', '345.678.901-23'),
(108, 'Diego Oliveira', '1122', '456.789.012-34'),
(109, 'Elisa Souza', '3345', '567.890.123-45'),
(110, 'Felipe Rocha', '5567', '678.901.234-56'),
(111, 'Gabriela Lima', '7890', '789.012.345-67'),
(112, 'Hugo Fernandes', '2345', '890.123.456-78'),
(113, 'Isabela Costa', '6789', '901.234.567-89'),
(114, 'João Pereira', '3456', '012.345.678-90'),
(115, 'Karina Santos', 'abcd', '123.456.789-01'),
(116, 'Leonardo Silva', '2346', '234.567.890-12'),
(117, 'Mariana Oliveira', '6780', '345.678.901-23'),
(118, 'Nathan Rocha', '3457', '456.789.012-34'),
(119, 'Olivia Santos', '7892', '567.890.123-45'),
(120, 'Pedro Lima', '2348', '678.901.234-56'),
(121, 'Quésia Costa', '5679', '789.012.345-67'),
(122, 'Rafael Souza', '6781', '890.123.456-78'),
(123, 'Sabrina Fernandes', '2340', '901.234.567-89'),
(124, 'Thiago Rocha', '3458', '012.345.678-90'),
(125, 'Ursula Lima', 'abcd', '123.456.789-01'),
(126, 'Vinícius Pereira', '1357', '234.567.890-12'),
(127, 'William Souza', '2468', '345.678.901-23'),
(128, 'Xavier Costa', '1122', '456.789.012-34'),
(129, 'Yasmin Oliveira', '2343', '567.890.123-45'),
(130, 'Zuleika Rocha', '5671', '678.901.234-56'),
(131, 'Ana Paula', '4321', '789.012.345-67'),
(132, 'Bruno Lima', '8900', '890.123.456-78'),
(133, 'Camila Oliveira', '9087', '901.234.567-89'),
(134, 'Daniel Rocha', '6541', '012.345.678-90'),
(135, 'Eduarda Souza', 'abcd', '123.456.789-01'),
(136, 'Felipe Pereira', '2310', '234.567.890-12'),
(137, 'Gabriela Rocha', '9988', '345.678.901-23'),
(138, 'Hugo Lima', '4321', '456.789.012-34'),
(139, 'Isabela Souza', '7654', '567.890.123-45'),
(140, 'João Lima', '8765', '678.901.234-56'),
(141, 'Karina Pereira', '2233', '789.012.345-67'),
(142, 'Leonardo Costa', '5566', '890.123.456-78'),
(143, 'Mariana Rocha', '4455', '901.234.567-89'),
(144, 'Nathan Lima', '6677', '012.345.678-90'),
(145, 'Olivia Pereira', 'abcd', '123.456.789-01'),
(146, 'Pedro Souza', '1112', '234.567.890-12'),
(147, 'Quésia Rocha', '4567', '345.678.901-23'),
(148, 'Rafael Lima', '3232', '456.789.012-34'),
(149, 'Sabrina Souza', '2323', '567.890.123-45'),
(150, 'Thiago Pereira', '5647', '678.901.234-56'),
(151, 'Ursula Costa', '2345', '789.012.345-67'),
(152, 'Vinícius Rocha', '6712', '890.123.456-78'),
(153, 'William Costa', '1120', '901.234.567-89'),
(154, 'Xavier Lima', '9823', '012.345.678-90'),
(155, 'Yasmin Pereira', 'abcd', '123.456.789-01'),
(156, 'Zuleika Souza', '3030', '234.567.890-12'),
(157, 'Ana Cristina', '4567', '345.678.901-23'),
(158, 'Bruno Martins', '2340', '456.789.012-34'),
(159, 'Camila Souza', '5566', '567.890.123-45'),
(160, 'Diego Pereira', '1123', '678.901.234-56'),
(161, 'Elisa Costa', '9801', '789.012.345-67'),
(162, 'Felipe Rocha', '7799', '890.123.456-78'),
(163, 'Gabriela Pereira', '6543', '901.234.567-89'),
(164, 'Hugo Souza', '3231', '012.345.678-90'),
(165, 'Isabela Rocha', 'abcd', '123.456.789-01'),
(166, 'João Pereira', '6669', '234.567.890-12'),
(167, 'Karina Costa', '8899', '345.678.901-23'),
(168, 'Leonardo Lima', '1230', '456.789.012-34'),
(169, 'Mariana Costa', '6783', '567.890.123-45'),
(170, 'Nathan Souza', '2344', '678.901.234-56'),
(171, 'Olivia Rocha', '9877', '789.012.345-67'),
(172, 'Pedro Pereira', '1124', '890.123.456-78'),
(173, 'Quésia Souza', '2233', '901.234.567-89'),
(174, 'Rafael Costa', '3344', '012.345.678-90'),
(175, 'Sabrina Pereira', 'abcd', '123.456.789-01'),
(176, 'Thiago Costa', '6677', '234.567.890-12'),
(177, 'Ursula Souza', '7788', '345.678.901-23'),
(178, 'Vinícius Pereira', '8899', '456.789.012-34'),
(179, 'William Rocha', '9900', '567.890.123-45'),
(180, 'Xavier Souza', '1100', '678.901.234-56'),
(181, 'Yasmin Rocha', '2231', '789.012.345-67'),
(182, 'Zuleika Pereira', '3345', '890.123.456-78'),
(183, 'Ana Beatriz', '5555', '901.234.567-89'),
(184, 'Bruno Costa', '6660', '012.345.678-90'),
(185, 'Camila Pereira', 'abcd', '123.456.789-01'),
(186, 'Daniel Souza', '8882', '234.567.890-12'),
(187, 'Eduarda Rocha', '9993', '345.678.901-23'),
(188, 'Felipe Lima', '1237', '456.789.012-34'),
(189, 'Gabriela Costa', '2341', '567.890.123-45'),
(190, 'Hugo Pereira', '3456', '678.901.234-56'),
(191, 'Isabela Lima', '4560', '789.012.345-67'),
(192, 'João Costa', '5671', '890.123.456-78'),
(193, 'Karina Lima', '6782', '901.234.567-89'),
(194, 'Leonardo Pereira', '7891', '012.345.678-90'),
(195, 'Mariana Lima', 'abcd', '123.456.789-01'),
(196, 'Nathan Pereira', '9013', '234.567.890-12'),
(197, 'Olivia Lima', '1023', '345.678.901-23'),
(198, 'Pedro Rocha', '2034', '456.789.012-34'),
(199, 'Quésia Pereira', '3045', '567.890.123-45'),
(200, 'Rafael Rocha', '4056', '678.901.234-56'),
(201, 'Sabrina Lima', '5067', '789.012.345-67'),
(202, 'Thiago Lima', '6078', '890.123.456-78'),
(203, 'Ursula Pereira', '7089', '901.234.567-89'),
(204, 'Vinícius Lima', '8090', '012.345.678-90');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id_vendas` int(11) NOT NULL,
  `produto_id_produto` int(11) DEFAULT NULL,
  `valor_venda` decimal(10,2) DEFAULT NULL,
  `data_venda` datetime DEFAULT NULL,
  `quantidade_vendida` int(11) DEFAULT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `forma_pagamento` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id_vendas`, `produto_id_produto`, `valor_venda`, `data_venda`, `quantidade_vendida`, `id_pedido`, `id_cliente`, `forma_pagamento`) VALUES
(134, 6, 6.00, '2025-04-16 16:49:09', 1, 156, 15, 'pix'),
(135, 7, 7.00, '2025-04-16 16:49:09', 1, 156, 15, 'pix'),
(136, 78, 12.00, '2025-04-16 16:50:18', 1, 157, 12, 'cartao'),
(137, 74, 11.00, '2025-04-16 16:50:18', 1, 157, 12, 'cartao'),
(138, 90, 10.00, '2025-04-16 16:50:45', 1, 158, 22, 'pix'),
(139, 87, 10.00, '2025-04-16 16:51:23', 1, 159, 25, 'pix'),
(140, 18, 9.00, '2025-04-16 16:51:23', 1, 159, 25, 'pix'),
(141, 22, 7.00, '2025-04-16 16:51:23', 1, 159, 25, 'pix'),
(142, 88, 12.00, '2025-04-16 16:52:19', 1, 160, 49, 'cartao'),
(143, 16, 7.00, '2025-04-16 16:52:56', 1, 161, 51, 'pix'),
(144, 21, 6.00, '2025-04-16 16:52:56', 1, 161, 51, 'pix'),
(145, 84, 4.00, '2025-04-16 16:52:56', 1, 161, 51, 'pix'),
(146, 39, 5.00, '2025-04-16 16:53:26', 1, 162, 48, 'pix'),
(147, 36, 8.00, '2025-04-16 16:53:26', 1, 162, 48, 'pix'),
(148, 27, 8.00, '2025-04-16 16:53:26', 1, 162, 48, 'pix'),
(149, 19, 10.00, '2025-04-16 16:54:07', 1, 163, 37, 'pix'),
(150, 18, 9.00, '2025-04-16 16:54:07', 1, 163, 37, 'pix'),
(151, 3, 7.00, '2025-04-16 16:54:56', 1, 164, 29, 'pix'),
(152, 4, 10.00, '2025-04-16 16:54:56', 1, 164, 29, 'pix'),
(153, 18, 9.00, '2025-04-27 13:33:13', 3, 165, 30, 'pix'),
(154, 18, 9.00, '2025-04-27 13:34:31', 2, 166, 30, 'pix'),
(155, 18, 9.00, '2025-04-27 13:35:03', 2, 167, 30, 'pix'),
(156, 66, 9.00, '2025-04-27 13:51:22', 1, 168, 30, 'pix'),
(157, 3, 3.50, '2025-04-30 14:17:41', 1, 169, 30, 'pix'),
(158, 4, 9.00, '2025-04-30 14:17:41', 1, 169, 30, 'pix'),
(159, 14, 9.00, '2025-04-30 14:51:19', 10, 170, 30, 'pix'),
(160, 12, 7.00, '2025-04-30 16:21:46', 10, 171, 30, 'pix'),
(161, 12, 7.00, '2025-04-30 16:21:54', 6, 172, 30, 'pix'),
(162, 19, 100.00, '2025-03-27 16:49:09', 10, 173, 30, 'pix'),
(163, 8, 8.00, '2025-05-05 13:46:54', 1, 174, 30, 'pix'),
(164, 7, 7.00, '2025-05-05 13:46:54', 2, 174, 30, 'pix'),
(165, 11, 6.00, '2025-05-05 13:46:54', 4, 174, 30, 'pix'),
(166, 11, 6.00, '2025-05-12 16:23:17', 1, 175, 30, 'pix'),
(167, 6, 6.00, '2025-05-12 16:23:17', 2, 175, 30, 'pix'),
(168, 7, 7.00, '2025-05-12 16:23:17', 1, 175, 30, 'pix'),
(169, 6, 6.00, '2025-05-12 16:34:40', 1, 176, 30, 'pix'),
(170, 4, 9.00, '2025-05-12 16:45:21', 1, 177, 30, 'cartao'),
(171, 66, 9.00, '2025-05-14 14:44:01', 1, 178, 30, 'pix');

--
-- Acionadores `vendas`
--
DELIMITER $$
CREATE TRIGGER `before_insert_vendas` BEFORE INSERT ON `vendas` FOR EACH ROW BEGIN
    IF NEW.quantidade_vendida <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Quantidade vendida inválida';
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`id_produto`);

--
-- Índices de tabela `relatorios`
--
ALTER TABLE `relatorios`
  ADD PRIMARY KEY (`id_relatorio`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id_vendas`),
  ADD KEY `fk_produto` (`produto_id_produto`),
  ADD KEY `fk_vendas_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT de tabela `produto`
--
ALTER TABLE `produto`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT de tabela `relatorios`
--
ALTER TABLE `relatorios`
  MODIFY `id_relatorio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id_vendas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE;

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `fk_produto` FOREIGN KEY (`produto_id_produto`) REFERENCES `produto` (`id_produto`),
  ADD CONSTRAINT `fk_vendas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
