interface Scripts {
  name: string;
  src: string;
}

export const ScriptStore: Scripts[] = [
  {name: 'fbSdk', src: 'https://connect.facebook.net/en_US/sdk.js'},
  {name: 'Stripe', src: 'https://js.stripe.com/v3/'},
];
