ALTER TABLE `ost_ticket` ADD `TTID` varchar(50) NOT NULL AFTER `ticketID`
ALTER TABLE `ost_ticket` ADD UNIQUE INDEX(`TTID`)