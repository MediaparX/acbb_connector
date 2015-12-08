# acbb_connector
BitBucket - ActiveCollab connector

This is a tool for connecting [BitBucket](https://bitbucket.org) (BB) repositories with [ActiveCollab](https://www.activecollab.com/) (AC) projects. It allows you to reference or solve AC tasks/tickets with BB commits by using keywords in your commit message. Currently the following features are supported:

* referencing tickets: creates a comment on the AC task with commit message, author and link to BB
* solving tickets: same as referencing, but marks task as *solved* and assigns it back to the delegating user

## Examples

### Referencing a ticket

The following commit message:

```
made default recipient configurable in config.ini
refs #187
```

would create a comment on the corresponding AC task like:

> made default recipient configurable in config.ini

> by Max Smith in this [BitBucket commit](https://bitbucket.org#link-to-commit)

### Solving a ticket

The following commit message:

```
fixed syntax error
fixes #188
```

would create the following comment on the AC task like:

> fixed syntax error

> * reassigned to Solomon Bernson

> * fixed by Max Smith in this [BitBucket commit](https://bitbucket.org#link-to-commit)

where *Solomon Bernson* would be the AC user that assigned the ticket to *Max Smith*.

*Notice: if your workflow isn't exactly like this, it's easy to change it to straight up e.g. close the ticket
or mark it as "Feedback required".*


## How does it work

### Foreword

Both BB and AC offer similar functionality, just not together.

* BB offers the same keywords (and a lot more) to reference issues, but only if you're using their issue tracker
* AC offers git integration via the source module, but it's not really what we're looking for

This tool does not rule out either of those features, it can be used instead of, or alongside the default workflows of AC and BB.

### What I was saying..

BB supports triggers on certain repo events. We use the post-commit hook to make a request to our `run.php` file. This file receives a json payload from BB with the most recent commits for a given repo. We parse this data and each commit message looking for the keywords mentioned above. When we find one, we match the BB repository name to the AC project name via the config and trigger certain actions via the API each AC has.

### Limitations

Since AC starts task IDs at 1 for every project, it's not possible to reference a ticket id absolutely to a project, meaning you can only link each BB repo to 1 AC project.

## Installation

### Files

**1** Clone or download this repository
**2** Create `config.php` by referring to `config.default.php`. This is what your `config.php` could look like:

```php
<?php

// get the default values
require dirname(__FILE__) . '/config.default.php';

$config = array_merge($config, array(
	'api_base_url'   => 'https://url-of-your-ac.yourdomain.com/api.php',
	'api_user_token' => '253-qNF8cn9V3driS6wzjUVzMVgZuTok4zRaJJaZ2lN3',
	'label_id_map'   => array(
		'solved' => 6,
		),
	'repo_project_map' => array(
		'acbb_connector' => 'acbb-connector',
		),
	));

return $config;
```

* we'll get to `api_base_url` and `api_user_token` in the next section
* `label_id_map` maps labels to their IDs in your AC instance. The easiest way to found out what your label IDs are is to change the label of a ticket and inspect the dropdown menu with the label names. The value of the `<option>` tags are the label IDs.
* `repo_project_map` maps your BB repo names to your AC project names. In this example, this would map the repo `acbb_connector` on BB to my AC project `acbb-connector`.

**3** Upload this tool to the webserver where your AC instance is running, preferably outside of your webroot:

```
public_html/
    active-collab.yourdomain.org/
      acbb_connector.php
acbb_connector/
```

**4** Create `active-collab.yourdomain.org/acbb_connector.php` and make it invoke `run.php`:

```php
<?php
require_once '../acbb_connector/run.php';
```

You should no be able to call up `active-collab.yourdomain.org/acbb_connector.php` and get a *403 Access Denied*.

### ActiveCollab

1. Create a dedicated ActiveCollab user, assign him to all projects you use with BitBucket
2. Visit the user's user profile page
3. Click "Options" (the gear icon on the top-right) > "API Settings"
4. Click "Add API-Token"
5. Name: *BitBucket Bot* (or whatever you like) / Company: *Your company* / read-only: *no*
6. Click the magnifying glass and copy the API URL and token to config.php

### BitBucket

1. Open your repo(s) on bitbucket.org
2. Go to Settings > Webhooks > Add webhook
3. Add a title (e.g. ActiveCollab push), set the URL to `active-collab.yourdomain.org/acbb_connector.php` and choose the "Repository push" trigger

### Debugging

* Errors are logged to `./error_log` in the same directory as `run.php`
* Set `$config['debug'] = true` for a bit more verbosity

## Links

* [ActiveCollab API](https://help-classic.activecollab.com/books/api/check-api-url.html)
