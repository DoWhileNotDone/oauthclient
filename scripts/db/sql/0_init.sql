\! echo "--- INIT SCRIPT ---"

DROP SCHEMA IF EXISTS oauthclient CASCADE;

DO
$do$
BEGIN
   IF NOT EXISTS (
      SELECT                       -- SELECT list can stay empty for this
      FROM   pg_catalog.pg_roles
      WHERE  rolname = 'oauthclient') THEN
      CREATE USER oauthclient WITH ENCRYPTED PASSWORD 'oauthclient';
   END IF;
END
$do$;

CREATE SCHEMA oauthclient AUTHORIZATION oauthclient;

\! echo "Creating Tables..."

\! echo "Creating Oauth Provider Table..."
CREATE TABLE oauthclient.oauth_providers (
	oauthprovider_id  SERIAL PRIMARY KEY,
  oauthprovider_key VARCHAR(32) NOT NULL UNIQUE,
  authorization_uri VARCHAR(255) NOT NULL UNIQUE,
  token_uri VARCHAR(255) NOT NULL UNIQUE,
  callback_uri VARCHAR(255) NOT NULL,
  client_id VARCHAR(70) NOT NULL UNIQUE,
  client_secret VARCHAR(70) NOT NULL,
	created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at timestamp NULL
);

select * from oauthclient.oauth_providers;
\! echo "Done!"

\! echo "Granting Schema Privs..."
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA oauthclient TO oauthclient;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA oauthclient TO oauthclient;
