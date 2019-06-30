
## Webhook com o Git

Below are instructions for updating the Helpdesk using GitHub's Webhook.


#### It is necessary to find out which user the web server is running on. There are several ways to do this, below we will mention some, always in the Linux environment:

- If you know the server is Apache,  use the command:

```
apachectl -S
```

- It is also possible, using this command:

```ShellSession
ps -ef | egrep '(httpd|apache2|apache|nginx)' | grep -v `whoami` | grep -v root | head -n1 | awk '{print $1}'
```

- Or, creating a php script and viewing by browser:

```PHP
<?php
$output = shell_exec('whoami 2>&1');
echo 'web server user: ' . $output;
?>
```

#### Create the key for the web server user (www, www-data, apache, etc. ), with the email of the owner of the Helpdezk repository cloned in gitHub:

```ShellSession
sudo -u web_server_user ssh-keygen -t rsa -b 4096 -C "xxxxxxx@yyyy.com"
```


#### The key will be generated in the user's home directory of the web server, to find, we use the command:

```ShellSession
getent passwd "web_server_user" | cut -d: -f6
```

Since web_server_user is the name of the user located in the previous item.

#### Copy the key by executing the following command:

```
cat /path_to_web_server_user_home/.ssh/id_rsa.pub
```

Copy the string starting at ssh-rsa until the end of the email

#### In GitHub go to Settings and then SSH and GPC Keys and register the new key.

#### Create a directory for the work dir. Go to the work dir and clone, using the web server's user.

 ```ShellSession
 sudo -u web_server_user git clone git@github.com:git_user/helpdezk.git .
 ```



