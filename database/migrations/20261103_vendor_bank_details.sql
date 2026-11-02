-- Add banking and payout disbursement columns to vendors table
ALTER TABLE `vendors` 
ADD COLUMN IF NOT EXISTS `bank_name` VARCHAR(100) NULL AFTER `remember_token`,
ADD COLUMN IF NOT EXISTS `account_title` VARCHAR(150) NULL AFTER `bank_name`,
ADD COLUMN IF NOT EXISTS `account_number` VARCHAR(50) NULL AFTER `account_title`,
ADD COLUMN IF NOT EXISTS `iban` VARCHAR(34) NULL AFTER `account_number`,
ADD COLUMN IF NOT EXISTS `raast_id` VARCHAR(50) NULL AFTER `iban`,
ADD COLUMN IF NOT EXISTS `cnic` VARCHAR(20) NULL AFTER `raast_id`;

