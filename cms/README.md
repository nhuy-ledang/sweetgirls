### What's included:
- Angular 14+ & Typescript
- Bootstrap 4+ & SCSS
- Responsive layout
- RTL support
- High resolution
- https://htmlstream.com/front-dashboard/project-teams.html
- https://select2.org/configuration/options-api
- NodeJs 14: https://nodejs.org/download/release/v14.21.3/
- NodeJs 18: https://nodejs.org/download/release/v18.18.1/
- Python: https://www.python.org/downloads/

Server Requirements
===================
```
PHP >= 5.6.4
OpenSSL PHP Extension
PDO PHP Extension
Mbstring PHP Extensionnpm 
Tokenizer PHP Extension
XML PHP Extension
```

Installing platform
===================

Step 1:
-------
#### Installation
- run installer script via `./install` or `bash install`

> Installer script is a bash script that runs list of commands one-by-one. It is created to simplify installation process.

At this point you can start developing your app.


#### Development
```bash
$ npm install && npm run build
```

#### Production
```bash
$ npm install && npm run build:prod
```

#### Staging
Step 1:
```bash
$ npm install && npm run build:staging
```

Step 2:
- Upload "/dist" folder to "/public/" folder
- Upload "/dist/assets" folder to "/public/" folder

#### Autoload and Rebuild Angular
- run installer script via `./autoload` or `bash autoload`

Step 2:
-------
## Install XMPP client
## https://github.com/legastero/stanza.io/blob/master/


cd node_modules/stanza.io
npm install
make
```

to build build/stanzaio.bundle[.min].js


Reference
===================
```sh
https://github.com/angular/angular-cli/wiki/serve
https://angular.io/cli/serve
```

Git
===================
```
git push origin master --no-verify
```

That said, to increase the memory, in the terminal where you run your Node.js process:
```
export NODE_OPTIONS="--max-old-space-size=8192"
```
Or for Windows:
```
set NODE_OPTIONS="--max-old-space-size=8192"
```
