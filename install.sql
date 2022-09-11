-- add column in user table
ALTER TABLE wcf1_user ADD bookmarks INT(10) DEFAULT 0;
ALTER TABLE wcf1_user ADD bookmarkShares INT(10) DEFAULT 0;

-- Bookmark
DROP TABLE IF EXISTS wcf1_bookmark;
CREATE TABLE wcf1_bookmark (
	bookmarkID			INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	editID				INT(10) default NULL,
	editName			VARCHAR(255) NOT NULL DEFAULT '',
	editTime			INT(10) default NULL,
	isExternal			TINYINT(1) NOT NULL DEFAULT 0,
	isPrivate			TINYINT(1) NOT NULL DEFAULT 0,
	objectID			INT(10) NOT NULL DEFAULT 0,
	remark				TEXT NOT NULL,
	shareFrom			VARCHAR(255) NOT NULL DEFAULT '',
	shareWith			TEXT NOT NULL,
	time				INT(10) DEFAULT 0,
	title				VARCHAR(255) NOT NULL DEFAULT '',
	type				VARCHAR(20) NOT NULL DEFAULT '',
	url					TEXT NOT NULL,
	userID				INT(10),
	username			VARCHAR(255) NOT NULL DEFAULT '',
	
	KEY (type),
	KEY (userID)
);

DROP TABLE IF EXISTS wcf1_bookmark_share;
CREATE TABLE wcf1_bookmark_share (
	shareID				INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	accepted			TINYINT(1) NOT NULL DEFAULT 0,
	bookmarkID			INT(10) NOT NULL,
	lastVisitTime		INT(10) NOT NULL DEFAULT 0,
	remark				MEDIUMTEXT,
	receiverID			INT(10) NOT NULL,
	receiverName		VARCHAR(255) NOT NULL DEFAULT '',
	refused				TINYINT(1) NOT NULL DEFAULT 0,
	time				INT(10) DEFAULT 0,
	userID				INT(10),
	username			VARCHAR(255) NOT NULL DEFAULT ''
	
	KEY (bookmarkID),
	KEY (receiverID),
	KEY (userID)
);

ALTER TABLE wcf1_bookmark ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE wcf1_bookmark_share ADD FOREIGN KEY (bookmarkID) REFERENCES wcf1_bookmark (bookmarkID) ON DELETE CASCADE;
ALTER TABLE wcf1_bookmark_share ADD FOREIGN KEY (receiverID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;
ALTER TABLE wcf1_bookmark_share ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
