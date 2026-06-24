-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mer. 01 juil. 2026 à 00:26
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `librarydb`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin_notes`
--

CREATE TABLE `admin_notes` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `note_content` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin_notes`
--

INSERT INTO `admin_notes` (`id`, `username`, `note_content`, `created_at`) VALUES
(1, 'System Admin', 'alo 1234', '2026-04-18 02:57:57'),
(2, 'System Admin', 'alo 1234', '2026-04-18 03:02:40'),
(3, 'System Admin', 'what', '2026-04-18 03:02:57'),
(5, 'System Admin', 'what about me', '2026-04-18 03:45:49'),
(9, 'dinhbac', 'i\'m nguyen ngoc huy', '2026-04-21 06:02:15'),
(10, 'admin', 'what', '2026-06-21 20:00:15'),
(11, 'admin', 'yo', '2026-06-26 23:36:03');

-- --------------------------------------------------------

--
-- Structure de la table `book`
--

CREATE TABLE `book` (
  `bookid` int(11) NOT NULL,
  `bookname` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `publisher` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `available` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `book`
--

INSERT INTO `book` (`bookid`, `bookname`, `author`, `publisher`, `quantity`, `category`, `available`) VALUES
(123, 'Java nâng cao', 'Thái Sơn', 'Uttutt', 50, 'Giáo trình', 50),
(888, 'booktest', 'me', 'me', 15, 'CNTT', 15),
(908, 'Marketing', 'Hoa', 'Utt', 18, 'Marketing', 18),
(1412, 'Android', 'Bùi Như', 'UTT', 23, 'Công nghệ thông tin', 23),
(3457, 'Giao thông thông minh', 'haha', 'haha', 20, 'Nghiên cứu – Tham khảo', 20),
(3838, 'Kiểm thử phần mềm', 'Nguyễn Thị Thu Huệ', 'UTT', 12, 'CNTT', 12),
(9981, 'Triết Học', 'Thanh Nga', 'ĐH CN GTVT', 40, 'Đại Cương', 40);

-- --------------------------------------------------------

--
-- Structure de la table `borrow`
--

CREATE TABLE `borrow` (
  `borrowid` int(11) NOT NULL,
  `studentid` varchar(30) NOT NULL,
  `bookid` int(11) NOT NULL,
  `date_borrowed` date NOT NULL,
  `date_return` date DEFAULT NULL,
  `status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `borrow`
--

INSERT INTO `borrow` (`borrowid`, `studentid`, `bookid`, `date_borrowed`, `date_return`, `status`) VALUES
(4, '3457', 3457, '2025-12-09', '2025-12-31', 'RETURNED'),
(5, '888', 888, '2025-12-13', '2025-12-12', 'RETURNED'),
(6, '888', 888, '2025-12-02', '2026-01-20', 'RETURNED'),
(7, '3457', 888, '2025-12-08', '2026-01-02', 'RETURNED'),
(22, '888', 3457, '2025-12-08', '2026-01-29', 'RETURNED'),
(201, '9981', 9981, '2026-01-19', '2026-01-18', 'RETURNED'),
(202, '9981', 9981, '2026-01-13', '2026-01-15', 'RETURNED'),
(345, '1412', 888, '2026-01-15', '2026-01-11', 'RETURNED'),
(418, '66771508', 9981, '2026-04-18', '2026-04-18', 'RETURNED'),
(419, '66771508', 9981, '2026-04-18', '2026-04-25', 'RETURNED'),
(421, '66771508', 123, '2026-04-21', '2026-04-21', 'RETURNED'),
(555, '66771508', 123, '2000-04-21', '2001-04-21', 'RETURNED'),
(1412, '1412', 1412, '2026-01-01', '2026-01-22', 'RETURNED'),
(2026, '66771508', 908, '2025-01-13', '2026-01-20', 'RETURNED'),
(3838, '383838', 3838, '2026-06-26', '2026-06-04', 'RETURNED'),
(420420, '66771508', 123, '2026-04-20', '2026-04-20', 'RETURNED');

-- --------------------------------------------------------

--
-- Structure de la table `librarian`
--

CREATE TABLE `librarian` (
  `librarianid` int(11) NOT NULL,
  `librarianname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('ADMIN','LIBRARIAN') DEFAULT 'LIBRARIAN',
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `librarian`
--

INSERT INTO `librarian` (`librarianid`, `librarianname`, `email`, `address`, `phone`, `username`, `password`, `role`, `status`) VALUES
(0, 'System Admin', '', '', NULL, 'admin', 'admin123', 'ADMIN', 'ACTIVE'),
(555, 'kakarot', 'kakarot555@gmail.com', 'Korea', '1234567890', 'kakarot', 'kakarot123', 'LIBRARIAN', 'ACTIVE'),
(6677, 'emXinh', 'emxinh123@gmail.com', 'Hà Nội', '2938402874', 'emxinh', 'emxinh123', 'LIBRARIAN', 'ACTIVE'),
(7777, 'Dinh Bac', 'DinhBac777@gmail.com', 'Nghe An', '0977775431', 'dinhbac', '123456', 'ADMIN', 'ACTIVE'),
(420420, 'Nguyễn Tiến Mạnh', 'huytq@utt.edu.vn', 'Hà Nội', '0912345678', 'huyadmin', 'password123', 'ADMIN', 'ACTIVE'),
(445566, 'Leerfei', 'leerfei1234@gmail.com', 'KonTummm', '0909090909', 'leerfei', 'leerfei123', 'LIBRARIAN', 'ACTIVE'),
(555666, 'malphite', 'kakarot555666@gmail.com', 'Korea', '0987654321', 'malphite', 'malphite123', 'LIBRARIAN', 'ACTIVE');

-- --------------------------------------------------------

--
-- Structure de la table `student`
--

CREATE TABLE `student` (
  `studentid` int(11) NOT NULL,
  `studentname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `student`
--

INSERT INTO `student` (`studentid`, `studentname`, `email`, `address`, `gender`, `birthday`, `class`) VALUES
(222, 'malphite', 'zzz222@gmail.com', 'campuchia', 'Nam', '2016-12-22', '74DCTT22'),
(555, 'kakarot', 'kakarot999@gmail.com', 'Korea', 'Nam', '2016-12-22', '74DCTT22'),
(888, 'studenttest', 'studenttest888@gmail.com', 'Utt', 'Nữ', '2016-12-22', '74DCTT23'),
(1002, 'MonMonMon', 'Mon99@gmail.com', 'Nhật Bản', 'Nam', '2026-04-15', '81KT22'),
(1412, 'viet anh', 'vietanh1412@gmail.com', 'Ha Tinh', 'Nam', '2005-12-14', '74TTTT14'),
(2222, 'malphite', 'malphite@gmail.com', 'China', 'Nam', '2016-12-22', '74DCTT22'),
(9981, 'Thành', 'thanhkun9981@gmail.com', 'Ben Tre', 'Nam', '2004-01-15', '73QTMK22'),
(123123, 'Huydz', 'huydz123@gmail.com', 'Ha Noi', 'Nam', '2016-12-22', '74DCTT22'),
(383838, 'testcase1', 'testcase1@gmail.com', '131 Nguyễn Trãi', 'Nữ', '2026-06-03', '74DCTT38'),
(456654, 'Huypro', 'ok123@gmail.com', 'Thanh Hoa', 'Nam', '2016-12-22', '74DCTT22'),
(4182026, 'Leerfei', 'leerfei1234@gmail.com', 'KonTummm', 'Nam', '2026-04-10', '69CK12'),
(66771508, 'Tucampuchia', 'Tucampuchia@gmail.com', 'Nghe An', 'Nam', '2000-01-31', '74DCTT22');

-- --------------------------------------------------------

--
-- Structure de la table `system_log`
--

CREATE TABLE `system_log` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `action_detail` text DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `system_log`
--

INSERT INTO `system_log` (`id`, `username`, `role`, `action_type`, `action_detail`, `action_time`) VALUES
(1, 'test', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 20:58:22'),
(2, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-04 21:08:01'),
(3, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-04 21:08:56'),
(4, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-04 21:09:19'),
(5, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sinh viên', '2026-01-04 21:09:33'),
(6, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sinh viên', '2026-01-04 21:09:33'),
(7, 'malphite', 'LIBRARIAN', 'ADD_STUDENT', 'StudentID=1412, Name=viet anh', '2026-01-04 21:11:30'),
(8, 'malphite', 'LIBRARIAN', 'UPDATE_STUDENT', 'StudentID=3457, Name=babonnam', '2026-01-04 21:11:45'),
(9, 'malphite', 'LIBRARIAN', 'DELETE_STUDENT', 'StudentID=3457, Name=babonnam', '2026-01-04 21:12:00'),
(10, 'malphite', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý sinh viên', '2026-01-04 21:12:04'),
(11, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:12:44'),
(12, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:12:44'),
(13, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-04 21:23:42'),
(14, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:23:50'),
(15, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:23:50'),
(16, 'test', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:24:35'),
(17, 'test', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:24:45'),
(18, 'test', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:29:12'),
(19, 'test', 'ADMIN', 'ADD_BOOK', 'Thêm sách: Android', '2026-01-04 21:29:37'),
(20, 'test', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:37:45'),
(21, 'test', 'ADMIN', 'DELETE_BOOK', 'Xóa sách: Android', '2026-01-04 21:38:48'),
(22, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-04 21:39:41'),
(23, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:40:17'),
(24, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:40:17'),
(25, 'kakarot', 'LIBRARIAN', 'ADD_BOOK', 'Thêm sách: ID=1412, Tên=Android', '2026-01-04 21:40:50'),
(26, 'kakarot', 'LIBRARIAN', 'UPDATE_BOOK', 'Cập nhật sách: Android', '2026-01-04 21:41:21'),
(27, 'kakarot', 'LIBRARIAN', 'UPDATE_BOOK', 'Cập nhật sách: Android', '2026-01-04 21:41:36'),
(28, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả', '2026-01-04 21:41:45'),
(29, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-04 21:41:45'),
(30, 'kakarot', 'LIBRARIAN', 'BORROW_BOOK', 'BorrowID=1412, StudentID=1412, BookID=1412', '2026-01-04 21:42:13'),
(31, 'kakarot', 'LIBRARIAN', 'RETURN_BOOK', 'BorrowID=1412, BookID=1412', '2026-01-04 21:42:47'),
(32, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-04 21:48:27'),
(33, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:48:29'),
(34, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-04 21:48:29'),
(35, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả', '2026-01-04 21:48:30'),
(36, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-04 21:48:30'),
(37, 'kakarot', 'LIBRARIAN', 'BORROW_BOOK', 'BorrowID=345, StudentID=1412, BookID=888', '2026-01-04 21:49:01'),
(38, 'kakarot', 'LIBRARIAN', 'RETURN_BOOK', 'BorrowID=22, BookID=3457', '2026-01-04 21:49:37'),
(39, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả', '2026-01-04 21:50:05'),
(40, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-04 21:50:05'),
(41, 'test', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-04 21:51:26'),
(42, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-04 21:51:50'),
(43, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả', '2026-01-04 21:51:52'),
(44, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-04 21:51:52'),
(45, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả', '2026-01-04 21:52:15'),
(46, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-04 21:52:15'),
(47, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-04 21:55:32'),
(48, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-05 12:36:18'),
(49, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-05 12:36:20'),
(50, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-05 12:36:20'),
(51, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng form đổi mật khẩu', '2026-01-05 12:36:25'),
(52, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-05 12:36:44'),
(53, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-05 12:36:46'),
(54, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-05 12:36:47'),
(55, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-05 12:36:47'),
(56, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả', '2026-01-05 12:36:48'),
(57, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-05 12:36:48'),
(58, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-05 12:36:49'),
(59, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-05 12:37:13'),
(60, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-05 12:37:13'),
(61, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-05 12:37:14'),
(62, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả', '2026-01-05 12:37:19'),
(63, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-05 12:37:19'),
(64, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-05 12:37:20'),
(65, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-05 12:38:39'),
(66, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sinh viên', '2026-01-05 12:38:40'),
(67, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý sinh viên', '2026-01-05 12:38:41'),
(68, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-05 12:38:42'),
(69, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-05 12:38:43'),
(70, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-05 12:38:44'),
(71, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-05 12:38:45'),
(72, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-05 12:38:46'),
(73, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-05 12:38:46'),
(74, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng form đổi mật khẩu', '2026-01-05 12:38:46'),
(75, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-05 12:39:50'),
(76, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sinh viên', '2026-01-05 12:39:52'),
(77, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sinh viên', '2026-01-05 12:39:53'),
(78, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-05 12:39:54'),
(79, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-05 12:39:54'),
(80, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-05 12:39:55'),
(81, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-05 12:39:56'),
(82, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-05 12:39:57'),
(83, 'System Admin', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng form đổi mật khẩu', '2026-01-05 12:39:58'),
(84, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-05 12:43:49'),
(85, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sinh viên', '2026-01-05 12:43:50'),
(86, 'malphite', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý sinh viên', '2026-01-05 12:43:51'),
(87, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-05 12:43:52'),
(88, 'malphite', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-05 12:43:52'),
(89, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-05 12:43:53'),
(90, 'malphite', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-05 12:43:54'),
(91, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-05 12:43:55'),
(92, 'malphite', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng form đổi mật khẩu', '2026-01-05 12:43:56'),
(93, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-05 12:49:51'),
(94, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-05 12:49:52'),
(95, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-05 12:49:53'),
(96, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-05 12:55:44'),
(97, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-05 12:55:46'),
(98, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-05 12:55:47'),
(99, 'kakarot', 'LIBRARIAN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-05 12:55:48'),
(100, 'kakarot', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng form đổi mật khẩu', '2026-01-05 12:55:49'),
(101, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-05 12:57:34'),
(102, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-05 12:57:36'),
(103, 'System Admin', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng form đổi mật khẩu', '2026-01-05 12:57:37'),
(104, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-05 13:01:17'),
(105, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-05 13:01:18'),
(106, 'malphite', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng form đổi mật khẩu', '2026-01-05 13:01:19'),
(107, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-20 06:08:41'),
(108, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-20 06:14:15'),
(109, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sinh viên', '2026-01-20 06:14:17'),
(110, 'System Admin', 'ADMIN', 'ADD_STUDENT', 'StudentID=9981, Name=Thành', '2026-01-20 06:16:17'),
(111, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sinh viên', '2026-01-20 06:16:43'),
(112, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:16:45'),
(113, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-20 06:20:04'),
(114, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:20:06'),
(115, 'System Admin', 'ADMIN', 'ADD_BOOK', 'Thêm sách: ID=9981, Tên=Triết học', '2026-01-20 06:20:34'),
(116, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-20 06:21:07'),
(117, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:21:08'),
(118, 'System Admin', 'ADMIN', 'BORROW_BOOK', 'BorrowID=201, StudentID=9981, BookID=9981', '2026-01-20 06:21:44'),
(119, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 06:22:25'),
(120, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:22:26'),
(121, 'System Admin', 'ADMIN', 'UPDATE_BOOK', 'Cập nhật sách: Triết học', '2026-01-20 06:22:46'),
(122, 'test', 'ADMIN', 'DELETE_BOOK', 'Xóa sách: Triết học', '2026-01-20 06:29:28'),
(123, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-20 06:39:20'),
(124, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:39:22'),
(125, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 06:41:07'),
(126, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:41:11'),
(127, 'System Admin', 'ADMIN', 'ADD_BOOK', 'Thêm sách: ID=9981, Tên=Triết Học', '2026-01-20 06:42:05'),
(128, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-20 06:42:29'),
(129, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:42:30'),
(130, 'System Admin', 'ADMIN', 'BORROW_BOOK', 'BorrowID=202, StudentID=9981, BookID=9981', '2026-01-20 06:42:54'),
(131, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 06:43:26'),
(132, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:43:27'),
(133, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-20 06:43:33'),
(134, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:43:34'),
(135, 'System Admin', 'ADMIN', 'RETURN_BOOK', 'BorrowID=202, BookID=9981', '2026-01-20 06:43:44'),
(136, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 06:43:50'),
(137, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:43:51'),
(138, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-20 06:43:52'),
(139, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:43:53'),
(140, 'System Admin', 'ADMIN', 'RETURN_BOOK', 'BorrowID=201, BookID=9981', '2026-01-20 06:44:04'),
(141, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 06:44:20'),
(142, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:44:20'),
(143, 'System Admin', 'ADMIN', 'UPDATE_BOOK', 'Cập nhật sách: Triết Học', '2026-01-20 06:44:28'),
(144, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-20 06:44:37'),
(145, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:44:37'),
(146, 'System Admin', 'ADMIN', 'RETURN_BOOK', 'BorrowID=7, BookID=888', '2026-01-20 06:44:49'),
(147, 'System Admin', 'ADMIN', 'RETURN_BOOK', 'BorrowID=345, BookID=888', '2026-01-20 06:44:57'),
(148, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 06:45:04'),
(149, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:45:05'),
(150, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:45:06'),
(151, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:45:07'),
(152, 'System Admin', 'ADMIN', 'UPDATE_BOOK', 'Cập nhật sách: booktest', '2026-01-20 06:45:15'),
(153, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-20 06:45:21'),
(154, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:45:22'),
(155, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 06:46:13'),
(156, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:46:17'),
(157, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-20 06:46:21'),
(158, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở form đổi mật khẩu', '2026-01-20 06:47:14'),
(159, 'System Admin', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng form đổi mật khẩu', '2026-01-20 06:47:15'),
(160, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:47:18'),
(161, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-20 06:50:48'),
(162, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sinh viên', '2026-01-20 06:50:51'),
(163, 'malphite', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý sinh viên', '2026-01-20 06:50:53'),
(164, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 06:50:54'),
(165, 'malphite', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý sách', '2026-01-20 06:50:55'),
(166, 'malphite', 'LIBRARIAN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 06:50:56'),
(167, 'malphite', 'LIBRARIAN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 06:50:57'),
(168, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-20 08:31:19'),
(169, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 08:31:24'),
(170, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 08:32:06'),
(171, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 08:32:08'),
(172, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 08:32:15'),
(173, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 08:32:29'),
(174, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sinh viên', '2026-01-20 08:32:30'),
(175, 'System Admin', 'ADMIN', 'ADD_STUDENT', 'StudentID=4444, Name=manhck', '2026-01-20 08:33:29'),
(176, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý sinh viên', '2026-01-20 08:33:33'),
(177, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở mượn trả sách', '2026-01-20 08:33:35'),
(178, 'System Admin', 'ADMIN', 'BORROW_BOOK', 'BorrowID=2, StudentID=4444, BookID=888', '2026-01-20 08:34:12'),
(179, 'System Admin', 'ADMIN', 'CLOSE_FORM', 'Đóng quản lý mượn trả', '2026-01-20 08:34:27'),
(180, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 08:36:24'),
(181, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở màn hình chính', '2026-01-20 08:57:05'),
(182, 'System Admin', 'ADMIN', 'OPEN_FORM', 'Mở quản lý sách', '2026-01-20 08:57:16'),
(184, 'admin', NULL, 'SỬA', 'Đã cập nhật thông tin sinh viên MSSV: 421421', '2026-04-20 22:36:54'),
(185, 'admin', NULL, 'XÓA', 'Đã xóa hồ sơ sinh viên MSSV: 421421', '2026-04-20 22:37:04'),
(186, 'admin', NULL, 'THÊM SÁCH', 'Nhập kho sách mới: TFT VN 2027 (Mã: 412412, SL: 17)', '2026-04-20 22:43:11'),
(187, 'admin', NULL, 'SỬA SÁCH', 'Cập nhật thông tin sách: TFT VN 2027 (Mã: 412412)', '2026-04-20 22:43:19'),
(188, 'admin', NULL, 'XÓA SÁCH', 'Đã xóa đầu sách: TFT VN 2027 (Mã: 412412)', '2026-04-20 22:43:28'),
(189, 'admin', NULL, 'MƯỢN SÁCH', 'Cho SV Tucampuchia mượn cuốn: Java nâng cao (Mã mượn: 421)', '2026-04-20 22:51:28'),
(190, 'admin', NULL, 'TRẢ SÁCH', 'Nhận lại sách \'Java nâng cao\' từ SV Tucampuchia (Mã mượn: 421)', '2026-04-20 22:51:33'),
(191, 'malphite', NULL, 'LOGIN', 'Thủ thư malphite (malphite) đã đăng nhập hệ thống.', '2026-04-20 22:58:23'),
(192, 'emxinh', NULL, 'LOGIN', 'Thủ thư emXinh (emxinh) đã đăng nhập hệ thống.', '2026-04-20 23:01:30'),
(193, 'dinhbac', NULL, 'LOGIN', 'Thủ thư Dinh Bac (dinhbac) đã đăng nhập hệ thống.', '2026-04-20 23:01:47'),
(194, 'kakarot', NULL, 'LOGIN', 'Thủ thư kakarot (kakarot) đã đăng nhập hệ thống.', '2026-04-20 23:02:43'),
(195, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-04-20 23:02:58'),
(196, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-04-20 23:03:33'),
(197, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-04-21 00:27:48'),
(198, 'admin', NULL, 'LOGOUT', 'Thủ thư System Admin (admin) đã đăng xuất khỏi hệ thống.', '2026-04-21 01:04:54'),
(199, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-04-21 01:12:01'),
(200, 'admin', NULL, 'XÓA SÁCH', 'Đã xóa đầu sách: Ngữ Văn (Mã: 999)', '2026-04-21 01:14:19'),
(201, 'admin', NULL, 'LOGOUT', 'Thủ thư System Admin (admin) đã đăng xuất khỏi hệ thống.', '2026-04-21 01:27:48'),
(202, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-04-21 01:28:15'),
(203, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-06-20 18:34:35'),
(204, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-06-21 12:58:49'),
(205, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-06-22 16:22:57'),
(206, 'admin', NULL, 'LOGOUT', 'Thủ thư System Admin (admin) đã thực hiện đăng xuất khỏi hệ thống ứng dụng.', '2026-06-22 16:41:10'),
(207, 'malphite', NULL, 'LOGIN', 'Thủ thư malphite (malphite) đã đăng nhập hệ thống.', '2026-06-22 16:41:21'),
(208, 'malphite', NULL, 'LOGOUT', 'Thủ thư malphite (malphite) đã thực hiện đăng xuất khỏi hệ thống ứng dụng.', '2026-06-22 16:53:49'),
(209, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-06-22 17:04:33'),
(210, 'admin', NULL, 'LOGIN', 'Thủ thư System Admin (admin) đã đăng nhập hệ thống.', '2026-06-22 17:13:07'),
(211, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form danh sách Quản lý Sinh viên', '2026-06-26 18:10:13'),
(212, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form danh sách Quản lý Sinh viên', '2026-06-26 18:10:15'),
(213, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form danh sách Quản lý Sinh viên', '2026-06-26 18:10:54'),
(214, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form danh sách Quản lý Sách', '2026-06-26 18:10:58'),
(215, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form danh sách Quản lý Mượn - Trả sách', '2026-06-26 18:11:00'),
(216, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form danh sách Quản lý Sinh viên', '2026-06-26 18:11:27'),
(217, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form Quản lý Sinh viên', '2026-06-26 18:14:22'),
(218, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form Quản lý Sinh viên', '2026-06-26 18:14:24'),
(219, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form Quản lý Sách', '2026-06-26 18:14:26'),
(220, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form Quản lý Mượn - Trả sách', '2026-06-26 18:14:28'),
(221, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form Quản lý Sinh viên', '2026-06-27 10:00:13'),
(222, 'admin', 'ADMIN', 'ACCESS', 'Truy cập Form Quản lý Sinh viên', '2026-06-27 10:03:35'),
(223, 'malphite', 'LIBRARIAN', 'ACCESS', 'Truy cập Form Quản lý Sinh viên', '2026-06-27 10:04:38'),
(224, 'malphite', 'LIBRARIAN', 'ACCESS', 'Truy cập Form Quản lý Sinh viên', '2026-06-27 10:04:44'),
(225, 'malphite', 'LIBRARIAN', 'SEARCH', 'Tìm kiếm Sinh viên với từ khóa: \'3\'', '2026-06-27 10:04:46'),
(226, 'malphite', 'LIBRARIAN', 'SEARCH', 'Tìm kiếm Sinh viên với từ khóa: \'38\'', '2026-06-27 10:04:46'),
(227, 'malphite', 'LIBRARIAN', 'ACCESS', 'Truy cập Form Quản lý Mượn - Trả sách', '2026-06-27 10:04:58');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin_notes`
--
ALTER TABLE `admin_notes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`bookid`);

--
-- Index pour la table `borrow`
--
ALTER TABLE `borrow`
  ADD PRIMARY KEY (`borrowid`);

--
-- Index pour la table `librarian`
--
ALTER TABLE `librarian`
  ADD PRIMARY KEY (`librarianid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentid`);

--
-- Index pour la table `system_log`
--
ALTER TABLE `system_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin_notes`
--
ALTER TABLE `admin_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `system_log`
--
ALTER TABLE `system_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
