# OAuth test client

This OAuth test client implements fify OAuth providers.

## Setup

By default this project doesn't expose any ports. Use a
`docker-compose.override.yml` to access caddy:

```yaml
version: "3.8"

services:
  caddy:
    ports:
        - "127.0.0.1:443:443"
```

You could also have another project reverse-proxy to this caddy by adding a
network and using `http://oauth-client:80` as upstream.


## Providers

Client setup for any OAuth server would expect the redirect URI to be
`https://oauth-client.localhost/redirect-uri` for this project to work.

### Equip4Ordi

The example provider is implemented in
[src/OAuth/Provider/E4oProvider.php](src/OAuth/Provider/E4oProvider.php).

### Setup
```dotenv
OAUTH_SERVER_URL="https://equip4ordi.at"
OAUTH_CLIENT_ID="your_client_id"
OAUTH_CLIENT_SECRET="your_very_secret_secret"
```

### Scopes

- user:read:profile
