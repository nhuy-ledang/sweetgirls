// The file contents for the current environment will overwrite these during build.
// The build system defaults to the dev environment which uses `environment.ts`, but if you do
// `ng build --env=prod` then `environment.prod.ts` will be used instead.
// The list of which env maps to which file can be found in `.angular-cli.json`.

export const environment = {
  production: false,
  SUB_FOLDER: false,
  APP_DOMAIN: 'local.gomart.vn',
  API_URL: 'http://local.gomart.vn/api/v1/backend',
  FACEBOOK: {
    APP_ID: '725293304339070',
    APP_VERSION: 'v9.0',
  },
  FILESYSTEM: {
    URL: 'http://local.gomart.vn/upload',
  },
  XMPP: {
    HOST: 'local.gomart.vn',
    WSURL: 'ws://local.gomart.vn:5280/ws',
    PASSWORD: 'gomart@Motila12345',
  },
};
