CREATE TABLE people (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    enabled TINYINT(1) NOT NULL,
    account_id VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=INNODB;

CREATE TABLE `groups` (
    slug VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (slug)
) ENGINE=INNODB;

CREATE TABLE memberships (
    group_slug VARCHAR(255) NOT NULL,
    person_id INT NOT NULL,
    created_at DATETIME NOT NULL,

    PRIMARY KEY (group_slug, person_id),

    FOREIGN KEY (group_slug)
        REFERENCES `groups` (slug)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    FOREIGN KEY (person_id)
        REFERENCES `people` (id)
        ON UPDATE CASCADE ON DELETE RESTRICT

) ENGINE=INNODB;