#### 1. C:\Windows\System32\drivers\etc\host
```
127.0.0.1 local.dsbd01.vn
127.0.0.1 localcms.dsbd01.vn
```

#### 2. C:\xampp\apache\conf\extra\httpd-vhosts.conf
```
<VirtualHost local.dsbd01.vn:80>
    DocumentRoot "E:/www/motilacorp/dsbd01/web"
    ServerName local.dsbd01.vn
    ServerAlias local.dsbd01.vn
</VirtualHost>
<VirtualHost localcms.dsbd01.vn:80>
    DocumentRoot "E:/www/motilacorp/dsbd01/cms/dist"
    ServerName localcms.dsbd01.vn
    ServerAlias localcms.dsbd01.vn
</VirtualHost>
```

#### 3. Install ssl: resources
```
<VirtualHost local.dsbd01.vn:443>
    DocumentRoot "E:/www/motilacorp/dsbd01/web"
    ServerName local.dsbd01.vn
    ServerAlias local.dsbd01.vn
    SSLEngine on
    SSLCertificateFile "crt/local.dsbd01.vn/server.crt"
    SSLCertificateKeyFile "crt/local.dsbd01.vn/server.key"
</VirtualHost>
```

#### How to change the URI (URL) for a remote Git repository?
```
git remote set-url origin https://huyd:A6rLvqZrxJaUdaPzDwrM@bitbucket.org/motilacorp/theme01.git
git fetch --all --prune
git reset --hard
git checkout master
git remote show origin
git branch --set-upstream-to=origin/master
git pull
```