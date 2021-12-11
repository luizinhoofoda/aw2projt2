cria o banco e executa essa tabela
CREATE TABLE `voucher` (
  `id` int(10) NOT NULL,
  `name` varchar(25) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `voucher`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

abrir o html
