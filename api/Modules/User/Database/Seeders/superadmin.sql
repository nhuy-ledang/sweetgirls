--
-- Create users
-- Account demo@gmail.com
-- Password 123456
--
INSERT INTO `users` (`id`, `username`, `email`, `phone_number`, `password`, `updated_at`, `created_at`) VALUES ('1', 'demo', 'demo@gmail.com', '12345678', '$2y$10$I2W3rOaZhJZmFyVV.FvCWO0N8yOnV8Wf6RAekE7nydK3MpIVM984S', '2018-09-09 21:35:09', '2018-09-09 21:35:09');
INSERT INTO `role_users` (`created_at`, `role_id`, `updated_at`, `user_id`) VALUES ('2018-09-09 21:35:09', '1', '2018-09-09 21:35:09', '1');
INSERT INTO `activations` (`code`, `user_id`, `completed`, `completed_at`, `updated_at`, `created_at`) VALUES ('462QEC0G1nWjhRPJXVtIewJwMjNZz1Ql', '1', '1', '2018-09-09 21:35:09', '2018-09-09 21:35:09', '2018-09-09 21:35:09');
