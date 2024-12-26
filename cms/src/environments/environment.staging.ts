// The file contents for the current environment will overwrite these during build.
// The build system defaults to the dev environment which uses `environment.ts`, but if you do
// `ng build --env=prod` then `environment.prod.ts` will be used instead.
// The list of which env maps to which file can be found in `.angular-cli.json`.

export const environment = {
  production: false,
  SUB_FOLDER: true,
  APP_DOMAIN: 'sweetgirl.vn',
  API_URL: '/api/v1/backend',
  FACEBOOK: {
    APP_ID: '725293304339070',
    APP_VERSION: 'v9.0',
  },
  FILESYSTEM: {
    URL: '//upload',
  },
  XMPP: {
    HOST: 'sweetgirl.vn',
    WSURL: 'wss://sweetgirl.vn:5443/ws',
    PASSWORD: 'sweetgirl@Enginer12345',
  },
};
