--
-- Create supper admin
--
INSERT INTO `usrs` (`id`, `group_id`, `email`, `calling_code`, `phone_number`, `password`, `first_name`, `last_name`, `username`, `gender`, `birthday`, `address`, `avatar`, `avatar_url`, `status`, `email_verified`, `phone_verified`, `password_failed`, `is_notify`, `is_sms`, `completed_at`, `last_login`, `last_provider`, `ip`, `device_platform`, `device_token`, `created_at`, `updated_at`, `deleted_at`) VALUES(1, NULL, 'admin@gmail.com', NULL, '12345678', '$2y$10$N9RL/RQP6DanExPacysyDenTBUcfsA3OdTQLPhlZ/YOjZrmElocRm', NULL, NULL, 'admin', 0, NULL, NULL, NULL, NULL, 'activated', 0, 0, 0, 1, 1, NULL, NULL, NULL, '127.0.0.1', NULL, NULL, '2018-09-09 14:35:09', '2018-09-09 14:35:09', NULL);
INSERT INTO `usrs` (`id`, `group_id`, `email`, `calling_code`, `phone_number`, `password`, `first_name`, `last_name`, `username`, `gender`, `birthday`, `address`, `avatar`, `avatar_url`, `status`, `email_verified`, `phone_verified`, `password_failed`, `is_notify`, `is_sms`, `completed_at`, `last_login`, `last_provider`, `ip`, `device_platform`, `device_token`, `created_at`, `updated_at`, `deleted_at`) VALUES(2, NULL, 'demo@gmail.com', NULL, '09797505588', '$2y$10$InmAAh6tbKhl9OwUzxvJducLZGqoAqX5WN4XhUi9rwgYAa1Y5Z1w6', 'Huy', 'Dang', 'demo', 0, NULL, NULL, '', NULL, 'activated', 0, 0, 0, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2021-10-02 03:12:50', '2021-10-02 03:12:50', NULL);
INSERT INTO `usr__roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES (1, 'super-admin', 'Chuyên Viên Lập Trình', NULL, '2019-06-11 19:10:27', '2022-08-10 07:45:52');
INSERT INTO `usr__roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES (2, 'admin', 'Quản trị', NULL, '2019-06-11 19:10:27', '2022-08-10 08:09:42');
INSERT INTO `usr__roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES (3, 'manager', 'Quản lý', NULL, '2019-06-11 19:10:27', '2022-08-10 02:55:26');
INSERT INTO `usr__roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES (4, 'accountant', 'Kế toán', NULL, '2019-06-11 19:10:27', '2022-08-10 08:09:48');
INSERT INTO `usr__roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES (5, 'sales', 'Bán hàng', NULL, '2019-06-11 19:10:27', '2022-08-10 02:58:17');
INSERT INTO `usr__roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`) VALUES (6, 'user', 'Thành viên', NULL, '2019-06-11 19:10:27', '2022-08-10 08:09:53');
INSERT INTO `usr__role_users` (`created_at`, `role_id`, `updated_at`, `user_id`) VALUES ('2018-09-09 21:35:09', '1', '2018-09-09 21:35:09', '1');
INSERT INTO `usr__activations` (`code`, `user_id`, `completed`, `completed_at`, `updated_at`, `created_at`) VALUES ('462QEC0G1nWjhRPJXVtIewJwMjNZz1Ql', '1', '1', '2018-09-09 21:35:09', '2018-09-09 21:35:09', '2018-09-09 21:35:09');
INSERT INTO `usr__groups` (`id`, `name`) VALUES (1, 'Admin'), (2, 'User'), (3, 'Poster'), (4, 'Content Creator'), (5, 'SEO');