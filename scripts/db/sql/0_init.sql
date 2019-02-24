\! echo "--- INIT SCRIPT ---"

DROP SCHEMA IF EXISTS oauthclient CASCADE;

DO
$do$
BEGIN
   IF NOT EXISTS (
      SELECT                       -- SELECT list can stay empty for this
      FROM   pg_catalog.pg_roles
      WHERE  rolname = 'oauthclient') THEN
      CREATE USER oauthserver WITH ENCRYPTED PASSWORD 'oauthclient';
   END IF;
END
$do$;

CREATE SCHEMA oauthclient AUTHORIZATION oauthclient;

\! echo "Creating Tables..."


\! echo "Granting Schema Privs..."
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA oauthclient TO oauthclient;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA oauthclient TO oauthclient;
