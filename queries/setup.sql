--
-- Setup module Tasks
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks_tasks`
--

CREATE TABLE IF NOT EXISTS `tasks_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idAccount` int(11) unsigned NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 inserted, 2 processing, 3 completed',
  `addDate` datetime NOT NULL,
  `addIdAccount` int(11) unsigned NOT NULL,
  `updDate` datetime DEFAULT NULL,
  `updIdAccount` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idAccount` (`idAccount`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Dumping data for table `settings_permissions`
--

INSERT IGNORE INTO `settings_permissions` (`id`, `module`, `action`, `description`, `locked`) VALUES
(NULL, 'tasks', 'tasks_usage', 'View and edit personal tasks', 0),
(NULL, 'tasks', 'tasks_view_all', 'View all accounts to do tasks', 0),
(NULL, 'tasks', 'tasks_edit_all', 'Edit all accounts to do tasks', 0);

-- --------------------------------------------------------

--
-- Constraints for dumped tables
--

ALTER TABLE `tasks_tasks`
  ADD CONSTRAINT `tasks_tasks_ibfk_1` FOREIGN KEY (`idAccount`) REFERENCES `accounts_accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------