DROP TABLE IF EXISTS forums;
DROP TABLE IF EXISTS moderators_rights;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS topics;
DROP TABLE IF EXISTS users;

CREATE TABLE forums (
 id BIGINT UNSIGNED AUTO_INCREMENT,
 name VARCHAR(255) NOT NULL,
 description TEXT NOT NULL,

 PRIMARY KEY(id)
) ENGINE InnoDB;

CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT,
 name VARCHAR(255) NOT NULL UNIQUE,
 password VARCHAR(255),
 email VARCHAR(255) NOT NULL,
 type SMALLINT UNSIGNED NOT NULL CHECK (type > 0 AND type < 4),
 avatar VARCHAR(255),

 PRIMARY KEY(id)
) ENGINE InnoDB;

CREATE TABLE moderators_rights (
 user_id BIGINT UNSIGNED NOT NULL,
 forum_id BIGINT UNSIGNED NOT NULL,

 UNIQUE(user_id, forum_id),
 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
 FOREIGN KEY (forum_id) REFERENCES forums (id) ON DELETE CASCADE
) ENGINE InnoDB;

CREATE TABLE topics (
 id BIGINT UNSIGNED AUTO_INCREMENT,
 forum_id BIGINT UNSIGNED NOT NULL,
 poster_id BIGINT UNSIGNED NOT NULL,
 title VARCHAR(255) NOT NULL,
 views_count BIGINT UNSIGNED NOT NULL DEFAULT 0,

 PRIMARY KEY(id),
 FOREIGN KEY (forum_id) REFERENCES forums (id) ON DELETE CASCADE,
 FOREIGN KEY (poster_id) REFERENCES users (id) ON DELETE CASCADE 
) ENGINE InnoDB;

CREATE TABLE posts (
 id BIGINT UNSIGNED AUTO_INCREMENT,
 forum_id BIGINT UNSIGNED NOT NULL REFERENCES forums (id) ON DELETE CASCADE,
 topic_id BIGINT UNSIGNED NOT NULL REFERENCES topics (id) ON DELETE CASCADE,
 poster_id BIGINT UNSIGNED NOT NULL REFERENCES users (id) ON DELETE CASCADE,
 content TEXT NOT NULL,
 posted_time TIMESTAMP,
 last_updated_time TIMESTAMP,

 PRIMARY KEY(id),
 FOREIGN KEY (forum_id) REFERENCES forums (id) ON DELETE CASCADE,
 FOREIGN KEY (topic_id) REFERENCES topics (id) ON DELETE CASCADE,
 FOREIGN KEY (poster_id) REFERENCES users (id) ON DELETE CASCADE 
) ENGINE InnoDB;