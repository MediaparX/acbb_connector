# acbb_connector
BitBucket - ActiveCollab connector

Todo: describe what this does

## Howto

### Installation

Todo: document installation

### ActiveCollab

1. Create a dedicated ActiveCollab user, assign him to all projects you use with BitBucket
2. Visit the user's user profile page
3. Click "Options" > "API Settings"
4. Click "Add API-Token"
5. Name "acbb_connector" / Company "MediaparX" / read-only: no
6. Click the magnifying glass and copy the API URL and token to config.php

### Config

1. Rename config.php.tpl to config.php
2. Todo: document config options

### BitBucket

1. Open your repo(s) on bitbucket.org
2. Go to Settings > Webhooks > Add webhook
3. Add a title (e.g. ActiveCollab push), URL (to the run.php on your server) and choose the "Repository push" trigger

## Links

* [ActiveCollab API](https://help-classic.activecollab.com/books/api/check-api-url.html)