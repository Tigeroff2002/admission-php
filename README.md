CREATE TABLE sessions (
    id VARCHAR(40) NOT NULL,
    last_activity INT NOT NULL,
	user_id INT NOT NULL,
	user_agent VARCHAR(400) NOT NULL,
	ip_address VARCHAR(40) NOT NULL,
    data TEXT NOT NULL,
	payload VARCHAR(400) NOT NULL,
    PRIMARY KEY (id)
);
