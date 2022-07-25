-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июл 25 2022 г., 18:34
-- Версия сервера: 5.7.27-30
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `u0904790_smoappbot`
--

-- --------------------------------------------------------

--
-- Структура таблицы `balance`
--

CREATE TABLE `balance` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(11) NOT NULL,
  `sum` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `delayed_posts`
--

CREATE TABLE `delayed_posts` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(11) NOT NULL,
  `sendtime` bigint(20) NOT NULL,
  `stop` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(11) NOT NULL,
  `serviceid` int(8) NOT NULL,
  `volume` int(11) NOT NULL,
  `sum` float NOT NULL,
  `times` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `smoorderid` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `paylinks`
--

CREATE TABLE `paylinks` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(11) NOT NULL,
  `times` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `sum` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `smoservices`
--

CREATE TABLE `smoservices` (
  `rowid` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `min` int(6) NOT NULL,
  `max` bigint(20) NOT NULL,
  `price` float NOT NULL,
  `category_id` int(11) NOT NULL,
  `code` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `temp_sess`
--

CREATE TABLE `temp_sess` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(11) NOT NULL,
  `serviceid` int(8) NOT NULL,
  `otipe` varchar(64) NOT NULL,
  `times` int(11) NOT NULL,
  `volume` int(11) NOT NULL,
  `waitpayment` int(2) NOT NULL,
  `page` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(20) NOT NULL,
  `fname` varchar(64) NOT NULL,
  `lname` varchar(64) NOT NULL,
  `username` varchar(64) NOT NULL,
  `ref` int(11) NOT NULL,
  `refbalance` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `balance`
--
ALTER TABLE `balance`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `delayed_posts`
--
ALTER TABLE `delayed_posts`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `paylinks`
--
ALTER TABLE `paylinks`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `smoservices`
--
ALTER TABLE `smoservices`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `temp_sess`
--
ALTER TABLE `temp_sess`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`rowid`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `balance`
--
ALTER TABLE `balance`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `delayed_posts`
--
ALTER TABLE `delayed_posts`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `paylinks`
--
ALTER TABLE `paylinks`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `smoservices`
--
ALTER TABLE `smoservices`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `temp_sess`
--
ALTER TABLE `temp_sess`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
