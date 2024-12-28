// The file contents for the current environment will overwrite these during build.
// The build system defaults to the dev environment which uses `environment.ts`, but if you do
// `ng build --env=prod` then `environment.prod.ts` will be used instead.
// The list of which env maps to which file can be found in `.angular-cli.json`.

export const environment = {
  production: false,
  SUB_FOLDER: false,
  APP_DOMAIN: 'local.sweetgirls.vn',
  API_URL: 'http://local.sweetgirls.vn/api/v1/backend',
  FACEBOOK: {
    APP_ID: '725293304339070',
    APP_VERSION: 'v9.0',
  },
  FILESYSTEM: {
    URL: 'http://local.sweetgirls.vn/upload',
  },
  XMPP: {
    HOST: 'local.sweetgirls.vn',
    WSURL: 'ws://local.sweetgirls.vn:5280/ws',
    PASSWORD: 'sweetgirls@Motila12345',
  },
};
